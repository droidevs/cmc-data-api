<?php

use App\Http\Middleware\AssignRequestId;
use App\Http\Middleware\AuditRequests;
use App\Http\Middleware\CacheDataResponses;
use App\Http\Middleware\RejectSuspiciousCrossSiteRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\OriginMismatchException;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(AssignRequestId::class);
        $middleware->append(AuditRequests::class);

        $middleware->trustHosts();
        // issues the config is not loaded in time
//        $middleware->preventRequestForgery(
//            originOnly: config('security.csrf.origin_only', false),
//            allowSameSite: config('security.csrf.allow_same_site', false),
//        );

        $middleware->preventRequestForgery(
            originOnly:false,
            allowSameSite: false,
        );

        $middleware->throttleApi('api');
        $middleware->api(prepend: RejectSuspiciousCrossSiteRequests::class);
        $middleware->api(append: CacheDataResponses::class);
        $middleware->web(append: 'throttle:web');
        $middleware->web(append: CacheDataResponses::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (OriginMismatchException $exception, Request $request) {
            if (! $request->expectsJson() && ! $request->is('api/*')) {
                return null;
            }

            return response()->json([
                'message' => 'Request origin is not trusted.',
                'request_id' => $request->attributes->get('request_id'),
            ], Response::HTTP_FORBIDDEN);
        });

        $exceptions->render(function (TokenMismatchException $exception, Request $request) {
            if (! $request->expectsJson() && ! $request->is('api/*')) {
                return null;
            }

            return response()->json([
                'message' => 'CSRF token mismatch.',
                'request_id' => $request->attributes->get('request_id'),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        });
    })->create();
