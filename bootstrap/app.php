<?php

use Illuminate\Http\Request;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
            $exceptions->render(function (Throwable $e) {
                if ($e instanceof ModelNotFoundException) {
                    if ($e->getPrevious() instanceof ModelNotFoundException){
                        return response()->json([
                            'message' => 'Sorry unable to find this record in our database',
                            'success' => false
                        ], Response::HTTP_NOT_FOUND);
                    }
                }
                if ($e instanceof QueryException) {
                    return response()->json([
                        'message' => $e->getMessage(),
                        'success' =>  false
                    ], Response::HTTP_EXPECTATION_FAILED);
                }
                if ($e instanceof BindingResolutionException) {
                    return response()->json([
                        'message' => $e->getMessage(),
                        'success' =>  false
                    ], Response::HTTP_EXPECTATION_FAILED);
                }
                if ($e instanceof ValidationException) {
                    return response()->json([
                        'message' => $e->getMessage(),
                        'success' =>  false
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }
                if ($e instanceof MethodNotAllowedHttpException) {
                    return response()->json([
                        'message' => $e->getMessage(),
                        'success' =>  false
                    ], Response::HTTP_METHOD_NOT_ALLOWED);
                }
                if ($e instanceof NotFoundHttpException) {
                    return response()->json([
                        'message' => 'Sorry request route not found.',
                        'data' => [
                            'route' => request()->path(),
                            'method' => request()->method(),
                            'time' => now()->toDateTimeString(),
                            'info' => [
                                'message' => 'For more details you can reach out to our engineering team',
                                'data' => [
                                    'email' => 'dev@'. config('app.name'). '.com',
                                    'phoneNumber' => '+2348100788859'
                                ]
                            ]
                        ],
                        'success' => false
                    ], Response::HTTP_NOT_FOUND);
                }
                if ($e instanceof AuthenticationException) {
                    return  response()->json([
                        'message' =>  $e->getMessage(),
                        'success' => false
                    ], Response::HTTP_UNAUTHORIZED);
                }
                return  response()->json([
                    'message' => 'Sorry there was an  error contact the backend team',
                    'error' => $e->getMessage(),
                    'data' => [
                        'route' => request()->path(),
                        'method' => request()->method(),
                        'time' => now()->toDateTimeString(),
                        'info' => [
                            'message' => 'For more details you can reach out to our engineering team',
                            'data' => [
                                'email' => 'dev@'. config('app.name'). '.com',
                                'phoneNumber' => '+2348100788859'
                            ]
                        ]
                    ],
                    'success' => false
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            });
        })->create();
