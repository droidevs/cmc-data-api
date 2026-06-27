<?php

declare(strict_types=1);

namespace App\Services;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * BaseService — shared query logic consumed by both ApiControllers and WebControllers.
 *
 * Instead of duplicating ->with(), ->filter(), ->paginate() calls in two controller
 * hierarchies, every resource's service encapsulates:
 *   - the base query (model + default eager loads)
 *   - filter application (delegates to the existing QueryFilter classes)
 *   - include resolution (same allowlist pattern as ApiController)
 *   - pagination (same clamped per_page logic)
 *
 * Controllers become 3-line thin shells:
 *   public function index(Request $request) {
 *       return view('poles.index', $this->service->list($request));
 *   }
 */
abstract class BaseService
{
    /** Eloquent model class string. @var class-string<Model> */
    abstract protected function modelClass(): string;

    /** QueryFilter class string. @var class-string<QueryFilter> */
    abstract protected function filterClass(): string;

    /** Default relations to always eager-load on list queries. @return array<string> */
    protected function defaultWith(): array
    {
        return [];
    }

    /** Relations eligible for on-demand loading via ?include=. @return array<string> */
    protected function allowedIncludes(): array
    {
        return [];
    }

    /** Default relations to load on single-resource show(). @return array<string> */
    protected function defaultShowWith(): array
    {
        return $this->defaultWith();
    }

    // ─── Public API ──────────────────────────────────────────────────────────

    /**
     * Return paginated, filtered, sorted list — shared by index() in both controller types.
     *
     * @return array{items: LengthAwarePaginator, filters: array<string,mixed>}
     */
    public function list(Request $request): array
    {
        $query = $this->baseQuery();

        $this->applyDefaultWith($query);
        $this->applyFilter($request, $query);
        $this->applyRequestedIncludes($request, $query);

        return [
            'items'   => $this->paginate($request, $query),
            'filters' => $request->only($this->filterClass()::filterKeys()),
        ];
    }

    /**
     * Return a single model with its default relations + any requested includes.
     */
    public function find(Request $request, Model|int|string $model): Model
    {
        if (! $model instanceof Model) {
            $model = ($this->modelClass())::findOrFail($model);
        }

        $includes = array_unique(array_merge(
            $this->defaultShowWith(),
            $this->resolveIncludes($request)
        ));

        if (! empty($includes)) {
            $model->load($includes);
        }

        return $model;
    }

    // ─── Helpers (override in subclasses if needed) ───────────────────────

    protected function baseQuery(): Builder
    {
        return ($this->modelClass())::query();
    }

    protected function applyDefaultWith(Builder $query): void
    {
        if (! empty($this->defaultWith())) {
            $query->with($this->defaultWith());
        }
    }

    protected function applyFilter(Request $request, Builder $query): void
    {
        (new ($this->filterClass())($request))->apply($query);
    }

    protected function applyRequestedIncludes(Request $request, Builder $query): void
    {
        $includes = $this->resolveIncludes($request);
        if (! empty($includes)) {
            $query->with($includes);
        }
    }

    protected function resolveIncludes(Request $request): array
    {
        $raw       = (string) $request->query('include', '');
        $requested = array_filter(array_map('trim', explode(',', $raw)));
        $allowed   = $this->allowedIncludes();

        return array_values(array_intersect($requested, $allowed));
    }

    protected function paginate(Request $request, Builder $query, int $default = 20): LengthAwarePaginator
    {
        $perPage = (int) $request->query('per_page', $default);
        $perPage = max(1, min(100, $perPage));

        return $query->paginate($perPage)->withQueryString();
    }

}
