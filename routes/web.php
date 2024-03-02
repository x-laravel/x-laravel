<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('auth')->name('auth.')->group(function () {
    Route::prefix('password')->name('password.')->group(function () {
        Route::prefix('forgot')->name('forgot.')->group(function () {
            Route::post('', [\App\Http\Controllers\Auth\Password\ForgotCTRL::class, 'forgot'])->name('index');
            Route::post('/reset', [\App\Http\Controllers\Auth\Password\ForgotCTRL::class, 'reset'])->name('reset');
        });
    });
});

Route::middleware('auth:users')->group(function () {
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::get('', [\App\Http\Controllers\Auth\IndexCTRL::class, 'me'])->name('index');
        Route::delete('', [\App\Http\Controllers\Auth\IndexCTRL::class, 'logout'])->name('logout');
    });
});
