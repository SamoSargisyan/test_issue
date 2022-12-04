<?php

namespace App\Exceptions;

use Illuminate\Support\Str;
use League\OAuth2\Server\Exception\OAuthServerException;
use Mintrocket\UploaderModule\App\Exceptions\BaseUploaderException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException as AuthException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Debug\Exception\FatalThrowableError;

/**
 * @OA\Schema(schema="ValidationException",
 *     @OA\Property(property="field", type="string", example="field"),
 *     @OA\Property(property="message", type="string", example="VALIDATION_EXCEPTION"),
 *     @OA\Property(property="description", type="string", example="Человеко-понятное описание"),
 * )
 * @OA\Schema(schema="AuthorizationException",
 *     @OA\Property(property="message", type="string", example="AUTHORIZATION_EXCEPTION"),
 *     @OA\Property(property="description", type="string", example="Unauthenticated."),
 * )
 */
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        CustomException::class,
        OAuthServerException::class,
        AuthorizationException::class,
        AuthException::class,
        RouteNotFoundException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */

    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    protected $admin_routes = [
        '/api/v1/admin/*',
    ];

    protected function inAdminRoute($request)
    {
        foreach ($this->admin_routes as $admin_route) {
            if ($admin_route !== '/') {
                $admin_route = trim($admin_route, '/');
            }
            if ($request->fullUrlIs($admin_route) || $request->is($admin_route)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        $response = $this->inAdminRoute($request)
            ? $this->adminExceptionResponse($request, $exception)
            : $this->apiExceptionResponse($request, $exception);

        return $response ?? parent::render($request, $exception);
    }

    private function adminExceptionResponse($request, Throwable $exception)
    {
        if ($exception instanceof CustomException) {
            if ($request->ajax() || $request->wantsJson()) {
                $json = [
                    'status' => false,
                    'errors' => [],
                ];
                $json['errors'] = $exception->getData();
                return response()->json($json, $exception->getCodeException());
            }
        }
        // Если ошибка связана с валидацией
        if ($exception instanceof ValidationException) {
            if ($request->ajax() || $request->wantsJson()) {
                $json = [
                    'status' => false,
                    'errors' => [],
                ];
                foreach ($exception->errors() as $key => $error) {
                    $json['errors'][] = [
                        'code' => 422,
                        'field' => $key,
                        'message' => 'VALIDATION_EXCEPTION',
                        'description' => $error[0],
                    ];
                }

                return response()->json($json, 422);
            }
        }

        // Если ошибка связана с недостатком прав
        if ($exception instanceof AuthorizationException) {
            if ($request->ajax() || $request->wantsJson()) {
                $json = [
                    'status' => false,
                    'errors' => [],
                ];
                $json['errors'][] = [
                    'code' => 403,
                    'message' => 'AUTHORIZATION_EXCEPTION',
                    'description' => $exception->getMessage(), //$request->language == "ru" ? 'Доступ запрещен. Возможно объект принадлежит другому пользователю.' : 'Permission denied. Maybe the object belongs to another user.',
                ];
                return response()->json($json, 403);
            }
        }

        if ($exception instanceof AuthException) {
            if ($request->ajax() || $request->wantsJson()) {
                $json = [
                    'status' => false,
                    'errors' => [],
                ];
                $json['errors'][] = [
                    'code' => 401,
                    'message' => 'AUTHENTICATION_EXCEPTION',
                    'description' => $exception->getMessage(), //$request->language == "ru" ? 'Доступ запрещен. Возможно объект принадлежит другому пользователю.' : 'Permission denied. Maybe the object belongs to another user.',
                ];
                return response()->json($json, 401);
            }
        }

        // Если ошибка на сервере
        if ($exception instanceof FatalThrowableError) {
            if ($request->ajax() || $request->wantsJson()) {
                $json = [
                    'status' => false,
                    'errors' => [],
                ];
                $json['errors'][] = [
                    'code' => 500,
                    'message' => 'REAL_500', //,$exception->getMessage(),
                    'description' => $exception->getTrace(),
                ];
                return response()->json($json, 500);
            }
        }

        // Если ошибка в запросе
        if ($exception instanceof QueryException) {
            if ($request->ajax() || $request->wantsJson()) {
                $json = [
                    'status' => false,
                    'errors' => [],
                ];
                $json['errors'][] = [
                    'code' => 500,
                    'message' => 'QUERY_EXCEPTION',
                    'description' => $exception->getMessage(),
                    'trace' => $exception->getTrace(),
                ];
                return response()->json($json, 500);
            }
        }
    }

    private function apiExceptionResponse($request, Throwable $exception)
    {
        if($exception instanceof UnauthorizedHttpException) {
            return null;
        }
        if ($exception instanceof CustomException) {
            $json = $exception->getData();
            unset($json[0]['code']);
            return response()->json($json, $exception->getCodeException());
        }

        if ($exception instanceof BaseUploaderException) {
            $json = $exception->render();
            unset($json['code']);
            return response()->json([$json], $exception->getCode());
        }

        // Если ошибка связана с валидацией
        if ($exception instanceof ValidationException) {
            $json = [];
            foreach ($exception->errors() as $key => $error) {
                $json[] = [
                    'field' => $key,
                    'message' => 'VALIDATION_EXCEPTION',
                    'description' => $error[0],
                ];
            }
            return response()->json($json, 422);
        }

        // Если ошибка связана с недостатком прав
        if ($exception instanceof AuthorizationException) {
            $json = [[
                'message' => 'AUTHORIZATION_EXCEPTION',
                'description' => config('app.debug') ? $exception->getMessage() : 'AUTHORIZATION_EXCEPTION',
            ]];
            return response()->json($json, 403);
        }

        if ($exception instanceof AuthException) {
            $json = [[
                'message' => 'AUTHENTICATION_EXCEPTION',
                'description' => config('app.debug') ? $exception->getMessage() : 'AUTHENTICATION_EXCEPTION',
            ]];
            return response()->json($json, 401);
        }

        // Если ошибка на сервере
        if ($exception instanceof FatalThrowableError) {
            $json = [[
                'message' => 'REAL_500',
                'description' => config('app.debug') ? $exception->getMessage() : 'REAL_500',
                'trace' => config('app.debug') ? $exception->getTrace() : null,
            ]];
            return response()->json($json, 500);
        }

        // Если ошибка в запросе
        if ($exception instanceof QueryException) {
            $json = [[
                'message' => 'QUERY_EXCEPTION',
                'description' => config('app.debug') ? $exception->getMessage() : 'QUERY_EXCEPTION',
                'trace' => config('app.debug') ? $exception->getTrace() : null,
            ]];
            return response()->json($json, 500);
        }

        $acceptable = $request->getAcceptableContentTypes();
        $wantsHtml = isset($acceptable[0]) && Str::contains($acceptable[0], ['/html']);

        if ($wantsHtml) {
            return parent::render($request, $exception);
        }
        return response()->json([[
            'message' => 'REAL_500',
            'description' => config('app.debug') ? $exception->getMessage() : 'REAL_500',
            'trace' => config('app.debug') ? $exception->getTrace() : null,
        ]], 500);
    }

}
