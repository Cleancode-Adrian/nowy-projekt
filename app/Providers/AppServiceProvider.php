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

        // Globalny limit dla całego /api - wymagany przez throttleApi('api')
        // w bootstrap/app.php. Chroni publiczne endpointy przed scrapowaniem.
        RateLimiter::for('api', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(120)->by('api|user|' . $request->user()->id)
                : Limit::perMinute(40)->by('api|ip|' . $request->ip());
        });

        // Rate limiting for auth endpoints (brute-force protection)
        // Dwa limity naraz: per e-mail+IP (zgadywanie hasła jednego konta) oraz
        // per IP (credential stuffing - rotowanie adresów e-mail z jednego IP
        // omijało sam limit e-mail+IP).
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->input('email', '');
            return [
                Limit::perMinute(10)->by(strtolower($email) . '|' . $request->ip()),
                Limit::perMinute(30)->by('login-ip|' . $request->ip()),
            ];
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('admin-login', function (Request $request) {
            $email = (string) $request->input('email', '');
            return [
                Limit::perMinute(5)->by('admin|' . strtolower($email) . '|' . $request->ip()),
                Limit::perMinute(15)->by('admin-ip|' . $request->ip()),
            ];
        });

        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perMinute(5)->by('pwd|' . $request->ip());
        });

        RateLimiter::for('api-login', function (Request $request) {
            // API nie ma reCAPTCHA, więc limity są ostrzejsze niż na stronie.
            $email = (string) $request->input('email', '');
            return [
                Limit::perMinute(5)->by('api|' . strtolower($email) . '|' . $request->ip()),
                Limit::perMinute(20)->by('api-login-ip|' . $request->ip()),
            ];
        });

        RateLimiter::for('api-register', function (Request $request) {
            return Limit::perMinute(10)->by('api-reg|' . $request->ip());
        });
    }
}
