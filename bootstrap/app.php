<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Aplikacja stoi za nginx-em - bez tego $request->secure(), poprawne
        // przekierowania https i realne IP w rate limiterze nie działają.
        $middleware->trustProxies(
            at: explode(',', (string) env('TRUSTED_PROXIES', '127.0.0.1,::1')),
            headers: Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO,
        );

        // Ciasteczka ustawiane w przeglądarce przez skrypty firm trzecich - Laravel
        // nie może ich odszyfrowywać (dostałby null). Potrzebne do Meta CAPI:
        // zgoda z Cookiebota oraz identyfikatory Pixela.
        $middleware->encryptCookies(except: ['CookieConsent', '_fbp', '_fbc']);

        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
        ]);

        $middleware->append(SecurityHeaders::class);

        // W Laravelu 11+ throttle:api NIE jest domyślnie włączony.
        $middleware->throttleApi('api');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Nie logujemy pełnych stack trace'ów dla zwykłych 404/419 - zaśmiecają log.
        $exceptions->dontReport([
            Illuminate\Session\TokenMismatchException::class,
        ]);
    })->create();
