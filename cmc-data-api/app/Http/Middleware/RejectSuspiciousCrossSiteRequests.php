<?php

namespace App\Http\Middleware;

use App\Support\Security\SecurityContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RejectSuspiciousCrossSiteRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('security.cross_site_requests.enforce_for_api_writes', true) || $this->isReadRequest($request)) {
            return $next($request);
        }

        $origin = $request->headers->get('Origin');
        $refererOrigin = $this->originFromUrl($request->headers->get('Referer'));
        $secFetchSite = $request->headers->get('Sec-Fetch-Site');
        $originToCheck = $origin ?: $refererOrigin;

        $blocked = false;
        $reason = null;

        if ($originToCheck && ! SecurityContext::isTrustedOrigin($originToCheck)) {
            $blocked = true;
            $reason = 'untrusted_origin';
        } elseif ($secFetchSite === 'cross-site' && ! SecurityContext::isTrustedOrigin($originToCheck)) {
            $blocked = true;
            $reason = 'cross_site_write';
        } elseif ($secFetchSite === 'same-site' && ! config('security.cross_site_requests.allow_same_site', true)) {
            $blocked = true;
            $reason = 'same_site_write_disabled';
        } elseif (! $originToCheck && ! $secFetchSite && config('security.cross_site_requests.require_origin_header', false)) {
            $blocked = true;
            $reason = 'missing_origin';
        }

        if (! $blocked) {
            return $next($request);
        }

        $requestId = $request->attributes->get('request_id');

        Log::channel(config('security.audit.channel', 'audit'))->warning('security.cross_site_request_blocked', [
            'request_id' => $requestId,
            'reason' => $reason,
            'method' => $request->method(),
            'path' => $request->path(),
            'route' => SecurityContext::routeKey($request),
            'client' => SecurityContext::clientKey($request),
            'origin' => $origin,
            'referer_origin' => $refererOrigin,
            'sec_fetch_site' => $secFetchSite,
        ]);

        return response()->json([
            'message' => 'Cross-site write request blocked.',
            'request_id' => $requestId,
        ], Response::HTTP_FORBIDDEN);
    }

    private function isReadRequest(Request $request): bool
    {
        return in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true);
    }

    private function originFromUrl(?string $url): ?string
    {
        if (! is_string($url) || $url === '') {
            return null;
        }

        $parts = parse_url($url);

        if (! isset($parts['scheme'], $parts['host'])) {
            return null;
        }

        $origin = $parts['scheme'].'://'.$parts['host'];

        if (isset($parts['port'])) {
            $origin .= ':'.$parts['port'];
        }

        return $origin;
    }
}
