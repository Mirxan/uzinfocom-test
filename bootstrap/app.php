<?php

use App\Http\Middleware\Header;
use App\Http\Middleware\ApiPermission;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(append: [
            Header::class,
        ]);

        $middleware->alias([
            'api.permission' => ApiPermission::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Exception $exception) {
            if ($exception instanceof AuthenticationException) {
                return response()->errorResponse('Unauthorized', code: 401);
            }

            if ($exception instanceof AuthorizationException) {
                return response()->errorResponse('Permission denied', code: 403);
            }

            if ($exception instanceof ModelNotFoundException) {
                return response()->errorResponse('Request not found', code: 404);
            }

            if ($exception instanceof NotFoundHttpException) {
                return response()->errorResponse('Http route not found', code: 404);
            }

            if ($exception instanceof RouteNotFoundException) {
                return response()->errorResponse('Route not found', code: 404);
            }

            if ($exception instanceof MethodNotAllowedHttpException) {
                return response()->errorResponse('Method not found', code: 405);
            }

            if ($exception instanceof ValidationException) {
                return response()->errorResponse($exception?->validator?->errors()?->messages(), code: 422);
            }

            return response()->errorResponse($exception->getMessage(), code: 500);
        });
    })->create();
