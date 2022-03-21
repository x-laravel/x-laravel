<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\System\User as Model;
use Illuminate\Support\Facades\Hash;

class IndexCTRL extends Controller
{
    const ERRORS = [
        1 => 'E-posta veya şifre yanlış!',
    ];

    public function me(): \Illuminate\Http\JsonResponse
    {
        return response()->success(auth()->user());
    }

    public function authenticate(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = Model::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->error(1, 401);
        }

        return response()->success([
            'access_token' => $user->createToken($request->token_name ?: $request->getClientIp())->plainTextToken,
            'token_type' => 'Bearer',
            'expires_in' => config('sanctum.expiration', null) ? config('sanctum.expiration', null) * 60 : null,
        ]);
    }

    public function logout(): \Illuminate\Http\JsonResponse
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->success();
    }
}
