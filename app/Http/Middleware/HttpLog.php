<?php

namespace App\Http\Middleware;

use App\Models\Log\HttpLog as Model;
use Illuminate\Http\Request;

class HttpLog
{
    public function handle(Request $request, \Closure $next)
    {
        if (config('http-logger.active')) {
            $logModel = new Model();
            $logModel->method = strtoupper($request->getMethod());
            $logModel->uri = $request->getPathInfo();
            $logModel->headers = $request->headers->all();
            $logModel->body = $request->except(config('http-logger.except'));
            $logModel->ip_address = $request->getClientIp();
            $logModel->save();

            $request->logModel = $logModel;
        }

        return $next($request);
    }

    public function terminate(Request $request, $response)
    {
        if (isset($request->logModel)) {
            $logModel = $request->logModel;
            $logModel->response = $this->isJson($response) ? json_decode($response->getContent()) : null;
            $logModel->response_code = $response->getStatusCode();
            $logModel->elapsed_time = microtime(true) - LARAVEL_START;
            $logModel->update();
        }
    }

    private function isJson($string): bool
    {
        json_decode($string);
        return json_last_error() !== JSON_ERROR_NONE;
    }
}
