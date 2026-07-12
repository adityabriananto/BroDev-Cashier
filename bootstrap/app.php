<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $statusCode = 400;
                if ($e instanceof HttpExceptionInterface) {
                    $statusCode = $e->getStatusCode();
                } elseif ($e instanceof AuthenticationException) {
                    $statusCode = 401;
                } elseif ($e instanceof AuthorizationException) {
                    $statusCode = 403;
                }

                $code = null;
                $message = $e->getMessage();

                // Map business failures
                if (str_contains(strtolower($message), 'stock')) {
                    $code = 'insufficient_stock';
                } elseif ($statusCode === 401 || $statusCode === 403) {
                    $code = 'unauthorized_action';
                }

                $response = [
                    'status' => 'error',
                    'message' => $message,
                ];

                if ($code !== null) {
                    $response['code'] = $code;
                }

                return response()->json($response, $statusCode);
            }
        });
    })->create();
