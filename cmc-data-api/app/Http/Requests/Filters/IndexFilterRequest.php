<?php

declare(strict_types=1);

namespace App\Http\Requests\Filters;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base class for every "list endpoint" filter request.
 *
 * Responsibilities (the actual security surface of QueryFilter::apply()):
 *
 *   1. Reject any query param that isn't explicitly allowlisted by the
 *      concrete resource request. QueryFilter::apply() silently *ignores*
 *      unknown params today, which is safe against SQL injection (params
 *      not in filters() never reach the query builder) but is NOT safe
 *      against:
 *        - typos that silently return unfiltered data (looks safe, isn't)
 *        - clients discovering/abusing undocumented params later added
 *        - DoS via huge/garbage query strings
 *      So this layer fails loudly (422) instead of silently ignoring.
 *
 *   2. Validate `sort` against the *exact* sortable() allowlist of the
 *      target QueryFilter, with proper "-field" syntax checking, so a
 *      bad sort value is rejected before reaching the DB rather than
 *      silently doing nothing (current behaviour) or (if sortable() were
 *      ever loosened to raw input) reaching whereRaw/orderByRaw.
 *
 *   3. Validate `include` against the controller's $allowedIncludes,
 *      bound to a maximum depth/count, so relation loading can't be
 *      abused for N+1 / memory-exhaustion DoS
 *      (?include=a.b.c.d.e.f.g.h... or 50 comma-separated relations).
 *
 *   4. Clamp `per_page` server-side as defence in depth (ApiController
 *      already clamps to [1,100], this rejects non-numeric/negative
 *      input explicitly instead of silently coercing it).
 *
 * Concrete subclasses only need to declare:
 *   - filterRules(): the field-level validation rules for this resource's
 *     QueryFilter params (the part that's actually resource-specific)
 *   - sortable(): exact mirror of the QueryFilter::sortable() allowlist
 *   - allowedIncludes(): exact mirror of the controller's $allowedIncludes
 */
abstract class IndexFilterRequest extends FormRequest
{
    /** Hard ceiling regardless of what the controller's paginate() default is. */
    protected const MAX_PER_PAGE = 100;

    /** Hard ceiling on how many dotted segments a single include path may have. */
    protected const MAX_INCLUDE_DEPTH = 4;

    /** Hard ceiling on how many include paths may be requested at once. */
    protected const MAX_INCLUDE_COUNT = 10;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Resource-specific filter param rules — must mirror the *param keys*
     * declared in the matching App\Filters\*Filter::filters() map.
     *
     * @return array<string, mixed>
     */
    abstract protected function filterRules(): array;

    /**
     * Exact allowlist of columns the resource's QueryFilter::sortable()
     * exposes. Used to validate `sort=field,-other`.
     *
     * @return array<int, string>
     */
    abstract protected function sortable(): array;

    /**
     * Exact allowlist of relation paths the controller's $allowedIncludes
     * exposes. Used to validate `include=a,b.c`.
     *
     * @return array<int, string>
     */
    abstract protected function allowedIncludes(): array;

    /**
     * Merge resource rules with the shared pagination/sort/include rules.
     * Subclasses should NOT override this — override filterRules() instead.
     *
     * @return array<string, mixed>
     */
    final public function rules(): array
    {
        return array_merge($this->makeNullable($this->filterRules()), [
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:'.self::MAX_PER_PAGE],
            'page' => ['sometimes', 'integer', 'min:1'],
            'sort' => ['sometimes', 'string', 'max:200', $this->sortRule()],
            'include' => ['sometimes', 'string', 'max:500', $this->includeRule()],
        ]);
    }

    protected function makeNullable(array $rules): array
    {
        return array_map(function ($ruleSet) {
            $ruleSet = (array) $ruleSet;

            if (!in_array('nullable', $ruleSet, true)) {
                array_unshift($ruleSet, 'nullable');
            }

            return $ruleSet;
        }, $rules);
    }
    /**
     * Reject any query param not explicitly declared as a rule. This is
     * the main hardening over the current QueryFilter, which silently
     * drops unknown params — here we tell the caller instead of guessing.
     */
    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (ValidatorContract $validator) {
            $known = array_keys($this->rules());
            $unknown = array_diff(array_keys($this->query()), $known);

            if (! empty($unknown)) {
                $validator->errors()->add(
                    'query',
                    'Unsupported query parameter(s): '.implode(', ', $unknown).'. '
                    .'Allowed parameters: '.implode(', ', $known).'.'
                );
            }
        });
    }

    /** Validation closure for `sort=field,-other_field` against sortable(). */
    protected function sortRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) {
            $allowed = $this->sortable();

            foreach (array_filter(array_map('trim', explode(',', (string) $value))) as $segment) {
                $field = str_starts_with($segment, '-') ? substr($segment, 1) : $segment;

                if ($field === '' || ! in_array($field, $allowed, true)) {
                    $fail("Cannot sort by [{$field}]. Allowed sort fields: ".implode(', ', $allowed).'.');
                }
            }
        };
    }

    /** Validation closure for `include=a,b.c` against allowedIncludes(), with depth/count caps. */
    protected function includeRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) {
            $allowed = $this->allowedIncludes();
            $segments = array_values(array_filter(array_map('trim', explode(',', (string) $value))));

            if (count($segments) > self::MAX_INCLUDE_COUNT) {
                $fail('Too many include paths requested (max '.self::MAX_INCLUDE_COUNT.').');

                return;
            }

            foreach ($segments as $path) {
                if (substr_count($path, '.') + 1 > self::MAX_INCLUDE_DEPTH) {
                    $fail("Include path [{$path}] exceeds maximum depth of ".self::MAX_INCLUDE_DEPTH.'.');

                    continue;
                }

                if (! in_array($path, $allowed, true)) {
                    $fail("Cannot include [{$path}]. Allowed includes: ".implode(', ', $allowed).'.');
                }
            }
        };
    }

    /**
     * 422 with a structured, field-keyed error body instead of Laravel's
     * default redirect-back behaviour (which makes no sense for a JSON API).
     */
    protected function failedValidation(ValidatorContract $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Invalid filter/query parameters.',
            'errors' => $validator->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }

    /**
     * Convenience used by controllers if they want validated, type-coerced
     * filter input rather than re-pulling raw query() values.
     *
     * @return array<string, mixed>
     */
    public function validatedFilters(): array
    {
        return $this->safe()->only(array_keys($this->filterRules()));
    }
}
