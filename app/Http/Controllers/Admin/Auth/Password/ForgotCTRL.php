<?php

namespace App\Http\Controllers\Admin\Auth\Password;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\Password\Forgot\IndexRequest;
use App\Http\Requests\User\Auth\Password\Forgot\ResetRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ForgotCTRL extends Controller
{
    public function forgot(IndexRequest $request): \Illuminate\Http\JsonResponse
    {
        $status = Password::broker('admins')->sendResetLink(
            $request->only('email')
        );

        if ($status !== Password::RESET_LINK_SENT) {
            return response()->ctrlError($this, 1, trans($status), 400);
        }

        return response()->success();
    }

    public function reset(ResetRequest $request): \Illuminate\Http\JsonResponse
    {
        $status = Password::broker('admins')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) use ($request) {
                $user->forceFill(['password' => Hash::make($password)])->save();
                $user->setRememberToken(Str::random(10));

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->ctrlError($this, 2, trans($status), 400);
        }

        return response()->success();
    }
}
