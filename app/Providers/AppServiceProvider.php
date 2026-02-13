<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ustawienie polskiej lokalizacji dla dat
        \Carbon\Carbon::setLocale('pl');

        // Rate limiting for auth endpoints (brute-force protection)
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->input('email', '');
            return Limit::perMinute(10)->by(strtolower($email) . '|' . $request->ip());
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('admin-login', function (Request $request) {
            $email = (string) $request->input('email', '');
            return Limit::perMinute(5)->by('admin|' . strtolower($email) . '|' . $request->ip());
        });

        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perMinute(5)->by('pwd|' . $request->ip());
        });

        RateLimiter::for('api-login', function (Request $request) {
            $email = (string) $request->input('email', '');
            return Limit::perMinute(20)->by('api|' . strtolower($email) . '|' . $request->ip());
        });

        RateLimiter::for('api-register', function (Request $request) {
            return Limit::perMinute(10)->by('api-reg|' . $request->ip());
        });
    }
}
