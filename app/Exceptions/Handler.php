<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedByRequestDataException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function unauthenticated($request, AuthenticationException $e): JsonResponse
    {
        $response = $this->convertExceptionToArray($e, 401);
        $response['error']['description'] = $e->getMessage();
        return response()->json($response, 401);
    }

    protected function convertValidationExceptionToResponse(ValidationException $e, $request): JsonResponse
    {
        return response()->validationError($e);
    }

    protected function shouldReturnJson($request, Throwable $e): bool
    {
        return true;
    }

    protected function convertExceptionToArray(Throwable $e, $code = 500): array
    {
        $response = [
            'status' => false,
            'error' => [
                'type' => 'app',
                'code' => $this->isHttpException($e) ? $e->getStatusCode() : $code,
                'description' => $this->isHttpException($e) ? $e->getMessage() : trans('Server Error'),
            ],
            'elapsed_time' => microtime(true) - LARAVEL_START,
        ];

        if (config('app.debug')) {
            $response['input'] = request()->all();
            $response['queries'] = DB::getQueryLog();
            $response['error'] = array_merge($response['error'], [
                'description' => $e->getMessage(),
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => collect($e->getTrace())->map(function ($trace) {
                    return Arr::except($trace, ['args']);
                })->all(),
            ]);
        }

        if ($e::class === NotFoundHttpException::class) {
            $response['error']['description'] = trans('Not Found');
        } else if ($e::class === TenantCouldNotBeIdentifiedByRequestDataException::class) {
            $response['error']['description'] = trans('Tenant Not Found');
        }

        return $response;
    }
}
