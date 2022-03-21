<?php

use Illuminate\Support\Facades\Route;

Route::prefix('auth')->name('auth.')->group(function () {
    Route::prefix('password')->name('password.')->group(function () {
        Route::prefix('forgot')->name('forgot.')->group(function () {
            Route::post('', [\App\Http\Controllers\User\Auth\Password\ForgotCTRL::class, 'forgot'])->name('index');

            Route::post('/reset', [\App\Http\Controllers\User\Auth\Password\ForgotCTRL::class, 'reset'])->name('reset');
        });
    });
});

Route::middleware('auth:users')->group(function () {
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::get('', [\App\Http\Controllers\User\Auth\IndexCTRL::class, 'me'])->name('index');
        Route::delete('', [\App\Http\Controllers\User\Auth\IndexCTRL::class, 'logout'])->name('logout');
    });
});
