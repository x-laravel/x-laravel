<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ResponseMacroServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $prePare = function ($response) {
            $response['elapsed_time'] = microtime(true) - LARAVEL_START;

            if (config('app.debug')) {
                $response['input'] = request()->all();
                $response['queries'] = DB::getQueryLog();
            }

            return $response;
        };

        Response::macro('success', function ($data = null) use ($prePare) {
            if (empty($data)) {
                return Response::json($prePare([
                    'status' => true
                ]));
            }

            return Response::json($prePare([
                'status' => true,
                'data' => $data
            ]));
        });

        Response::macro('paginate', function (LengthAwarePaginator $paginator) use ($prePare) {
            return Response::json($prePare([
                'status' => true,
                'data' => $paginator->items(),
                'paginate' => [
                    'first' => 1,
                    'previous' => $paginator->currentPage() > 1 ? $paginator->currentPage() - 1 : null,
                    'current' => $paginator->currentPage(),
                    'next' => $paginator->lastPage() > $paginator->currentPage() ? $paginator->currentPage() + 1 : null,
                    'last' => $paginator->lastPage()
                ],
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total()
            ]));
        });


        Response::macro('error', function (int $code, $statusCode = 400) {
            $controller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['class'] ?: null;

            if (!is_subclass_of($controller, \App\Http\Controllers\Controller::class)) {
                return Response::customError('CTRL', 1, trans('An unknown error occurred from which controller!'), 500, [
                    'controller' => $controller,
                    'code' => $code,
                    'statusCode' => $statusCode,
                    'debug' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
                ]);
            }

            if (!defined($controller . '::ERRORS')) {
                return Response::customError('CTRL', 2, trans('Error list not defined in controller!'), 500, [
                    'controller' => $controller,
                    'code' => $code,
                    'statusCode' => $statusCode
                ]);
            }

            if (!isset(constant($controller . '::ERRORS')[$code])) {
                return Response::customError('CTRL', 3, trans('An undefined error code has been sent to the controller!'), 500, [
                    'controller' => $controller,
                    'code' => $code,
                    'statusCode' => $statusCode
                ]);
            }

            $type = getCtrlToErrorType($controller);
            $description = constant($controller . '::ERRORS')[$code];

            return Response::ctrlError($type, $code, $description, $statusCode);
        });

        Response::macro('ctrlError', function (string $type, int $code, string $description, int $statusCode) {
            return Response::customError($type, $code, $description, $statusCode);
        });

        Response::macro('customError', function (string $type, int $code, string $description, int $statusCode, array $detail = []) use ($prePare) {
            $response = [
                'status' => false,
                'error' => [
                    'type' => $type,
                    'code' => $code,
                    'description' => $description,
                ]
            ];

            if (config('app.debug') && count($detail)) {
                $response['error']['detail'] = $detail;
            }

            return Response::json($prePare($response), $statusCode);
        });

        Response::macro('validationError', function (ValidationException $e) use ($prePare) {
            $rules = [];
            foreach ($e->validator->getRules() as $name => $rule) {
                foreach ($rule as $index => $attr) {
                    if (Str::is(['exists:*', 'unique:*'], $attr)) {
                        unset($rule[$index]);
                    }
                }

                $rules[$name] = $rule;
            }

            return Response::json($prePare([
                'status' => false,
                'error' => [
                    'type' => 'form',
                    'code' => 400,
                    'description' => trans('Bad request.'),
                    'errors' => $e->validator->errors(),
                    'rules' => $rules,
                ]
            ]), 400);
        });
    }
}
