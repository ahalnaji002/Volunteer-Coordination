<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\VolunteerMiddleware;
use App\Http\Resources\ApiResponseResource;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'volunteer' => VolunteerMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $apiError = function (
            string $message,
            int $status,
            ?array $errors = null
        ) {
            $payload = [
                'status' => 'error',
                'message' => $message,
                'data' => null,
            ];

            if ($errors !== null) {
                $payload['errors'] = $errors;
            }

            return (new ApiResponseResource($payload))
                ->response()
                ->setStatusCode($status);
        };

        // Force API requests to return JSON responses.
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            return $request->is('api/*') || $request->expectsJson();
        });

        $exceptions->render(function (
            NotFoundHttpException $e,
            Request $request
        ) use ($apiError) {
            if ($request->is('api/*')) {
                if ($e->getPrevious() instanceof ModelNotFoundException) {
                    return $apiError('Resource not found.', 404);
                }

                return $apiError('Endpoint not found.', 404);
            }
        });

        $exceptions->render(function (
            AuthenticationException $e,
            Request $request
        ) use ($apiError) {
            if ($request->is('api/*')) {
                return $apiError('Unauthenticated.', 401);
            }
        });

        $exceptions->render(function (
            AuthorizationException $e,
            Request $request
        ) use ($apiError) {
            if ($request->is('api/*')) {
                return $apiError('Forbidden.', 403);
            }
        });

        $exceptions->render(function (
            ValidationException $e,
            Request $request
        ) use ($apiError) {
            if ($request->is('api/*')) {
                return $apiError(
                    'Validation failed.',
                    422,
                    $e->errors()
                );
            }
        });

        $exceptions->render(function (
            HttpExceptionInterface $e,
            Request $request
        ) use ($apiError) {
            if ($request->is('api/*')) {
                return $apiError(
                    match ($e->getStatusCode()) {
                        403 => 'Forbidden.',
                        405 => 'Method not allowed.',
                        429 => 'Too many requests.',
                        default => 'Request failed.',
                    },
                    $e->getStatusCode()
                );
            }
        });

        $exceptions->render(function (
            Throwable $e,
            Request $request
        ) use ($apiError) {
            if ($request->is('api/*')) {
                return $apiError('Server error.', 500);
            }
        });

    })->create();
