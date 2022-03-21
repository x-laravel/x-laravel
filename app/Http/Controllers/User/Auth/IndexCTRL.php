<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;

class IndexCTRL extends Controller
{
    public function me(): \Illuminate\Http\JsonResponse
    {
        return response()->success(auth()->user());
    }

    public function logout(): \Illuminate\Http\JsonResponse
    {
        auth()->user()->token()->revoke();

        return response()->success();
    }
}
