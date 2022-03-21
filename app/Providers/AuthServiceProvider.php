<?php

namespace App\Providers;

use App\Models\System\Admin;
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
            Passport::routes(null, ['prefix' => 'admin/oauth']);
            Passport::routes(null, ['prefix' => 'user/oauth', 'middleware' => 'tenant']);
        }
        Passport::loadKeysFrom(storage_path('secrets/oauth'));

        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            if (Admin::class === get_class($notifiable)) {
                $url = url(route('admin.auth.password.forgot.reset', [
                    'token' => $token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ], false));
                return str_replace(config('app.url') . '/admin', config('app.admin_panel_url'), $url);
            }

            $url = url(route('user.auth.password.forgot.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
            return str_replace(config('app.url') . '/user', config('app.user_panel_url'), $url);
        });
    }
}
