<?php

declare(strict_types=1);

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Base class for all resource filters.
 *
 * Subclasses declare a $filters map of "query param" => "handler method name"
 * (see each concrete filter for examples). Any query param not declared in
 * the map is silently ignored, so unknown params never blow up the request.
 *
 * Usage in a controller:
 *   $query = Stagiaire::query();
 *   (new StagiaireFilter($request))->apply($query);
 *
 * Design rationale: keeps controllers thin (no inline ->when() chains),
 * keeps each model's filterable surface declarative and testable in
 * isolation, and avoids a giant generic "operator DSL" the user didn't ask
 * for — every supported query param is explicit and self-documenting.
 */
abstract class QueryFilter
{
    public function __construct(protected Request $request)
    {
    }

    protected Builder $builder;

    /**
     * Map of query-string key => local method name responsible for it.
     *
     * @return array<string, string>
     */
    abstract protected function filters(): array;

    public static function filterKeys(): array
    {
        return array_keys((new static(new Request))->filters());
    }

    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach ($this->filters() as $param => $method) {
            if (! $this->request->filled($param)) {
                continue;
            }

            if (! method_exists($this, $method)) {
                continue;
            }

            $value = $this->request->input($param);

            $this->{$method}($value);
        }

        $this->applySort();

        return $this->builder;
    }

    // ─── Shared generic helpers (used by concrete filters) ────────────────────

    protected function exact(string $column, mixed $value): void
    {
        $this->builder->where($column, $value);
    }

    protected function like(string $column, mixed $value): void
    {
        $this->builder->where($column, 'like', "%{$value}%");
    }

    /** Accepts comma-separated list or array for whereIn. */
    protected function inList(string $column, mixed $value): void
    {
        $values = is_array($value) ? $value : array_filter(array_map('trim', explode(',', (string) $value)), fn ($v) => $v !== '');
        if (! empty($values)) {
            $this->builder->whereIn($column, $values);
        }
    }

    protected function min(string $column, mixed $value): void
    {
        $this->builder->where($column, '>=', $value);
    }

    protected function max(string $column, mixed $value): void
    {
        $this->builder->where($column, '<=', $value);
    }

    protected function boolean(string $column, mixed $value): void
    {
        $this->builder->where($column, $this->toBool($value));
    }

    protected function dateFrom(string $column, mixed $value): void
    {
        $this->builder->whereDate($column, '>=', $value);
    }

    protected function dateTo(string $column, mixed $value): void
    {
        $this->builder->whereDate($column, '<=', $value);
    }

    protected function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return in_array(mb_strtolower((string) $value), ['1', 'true', 'oui', 'yes', 'on'], true);
    }

    /**
     * Generic sort handling: ?sort=field,-other_field
     * A leading "-" means descending. Restricted to $this->sortable() allowlist.
     */
    protected function applySort(): void
    {
        $sortParam = (string) $this->request->input('sort', '');
        if ($sortParam === '') {
            return;
        }

        $allowed = $this->sortable();
        if (empty($allowed)) {
            return;
        }

        foreach (array_filter(array_map('trim', explode(',', $sortParam))) as $field) {
            $direction = 'asc';
            if (str_starts_with($field, '-')) {
                $direction = 'desc';
                $field = substr($field, 1);
            }

            if (in_array($field, $allowed, true)) {
                $this->builder->orderBy($field, $direction);
            }
        }
    }

    /**
     * Columns allowed in ?sort=. Override in concrete filters.
     *
     * @return array<int, string>
     */
    protected function sortable(): array
    {
        return [];
    }
}
