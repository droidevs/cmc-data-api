<?php

namespace App\Http\Middleware;

use App\Support\Security\SecurityContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AssignRequestId
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = (string) config('security.request_id_header', 'X-Request-Id');
        $requestId = SecurityContext::requestId($request);

        $request->attributes->set('request_id', $requestId);

        $response = $next($request);
        $response->headers->set($header, $requestId);

        return $response;
    }
}
