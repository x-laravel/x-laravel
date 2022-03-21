<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Passport::ignoreMigrations();
        if (!$this->app->routesAreCached()) {
            Passport::routes();
        }
        Passport::loadKeysFrom(storage_path('secrets/oauth'));

        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            $url = url(route('auth.password.forgot.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
            return str_replace(config('app.url'), config('app.web_url'), $url);
        });
    }
}
