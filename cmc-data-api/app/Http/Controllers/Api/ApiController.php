<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

abstract class ApiController extends Controller
{
    /**
     * Apply pagination in a consistent, bounded manner.
     */
    protected function paginate(Request $request, Builder $query, int $defaultPerPage = 15)
    {
        $perPage = (int) $request->query('per_page', $defaultPerPage);
        $perPage = max(1, min(100, $perPage));

        return $query->paginate($perPage);
    }

    /**
     * Parse `?include=a,b.c` and intersect with an allowlist.
     *
     * @param  array<int, string>  $allowed
     * @return array<int, string>
     */
    protected function requestedIncludes(Request $request, array $allowed): array
    {
        $includeRaw = (string) $request->query('include', '');
        $requested = array_values(array_filter(array_map('trim', explode(',', $includeRaw))));

        if (empty($requested)) {
            return [];
        }

        return array_values(array_intersect($requested, $allowed));
    }

    /**
     * Convenience: apply includes to a query if requested.
     *
     * @param  array<int, string>  $allowed
     */
    protected function withRequestedIncludes(Request $request, Builder $query, array $allowed): Builder
    {
        $includes = $this->requestedIncludes($request, $allowed);
        if (! empty($includes)) {
            $query->with($includes);
        }

        return $query;
    }
}

