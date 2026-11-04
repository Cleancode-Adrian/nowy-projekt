<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\UserRegistration;
use App\Support\FbPixel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            if (!Auth::user()->is_approved) {
                Auth::logout();
                return back()->withErrors(['email' => 'Twoje konto oczekuje na zatwierdzenie przez administratora.']);
            }

            return redirect()->intended(route('dashboard'))
                ->with('dl_event', ['event' => 'login', 'method' => 'email']);
        }

        return back()->withErrors([
            'email' => 'Nieprawidłowe dane logowania.',
        ])->onlyInput('email');
    }

    public function register(Request $request, UserRegistration $registration)
    {
        $validated = $request->validate(UserRegistration::rules(), UserRegistration::messages());

        try {
            $user = $registration->register($validated);
        } catch (\Throwable $e) {
            Log::error('User registration failed: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
                'request_data' => $request->except(['password', 'password_confirmation']),
            ]);

            return back()->withErrors([
                'email' => 'Wystąpił błąd podczas rejestracji. Spróbuj ponownie lub skontaktuj się z nami: biuro@cleancodeas.pl',
            ])->withInput();
        }

        // Zdarzenie konwersji dla GTM - wysyłane po redirectcie, więc trafia
        // do dataLayer dokładnie raz, po faktycznym zapisie konta.
        $dlEvent = [
            'event' => 'sign_up',
            'method' => 'email',
            'user_role' => $validated['role'] === 'client' ? 'klient' : 'wykonawca',
        ];

        FbPixel::flash('CompleteRegistration', [
            'status' => true,
            'content_name' => $dlEvent['user_role'],
        ], $user);

        if ($user->is_approved) {
            // Bez tego kroku osoba, która trafiła tu z reklamy, musiałaby
            // logować się drugi raz zaraz po założeniu konta.
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended(route('announcements.create'))
                ->with('success', 'Konto utworzone. Możesz od razu opisać swój projekt.')
                ->with('dl_event', $dlEvent);
        }

        return redirect()->route('login')
            ->with('success', 'Konto utworzone! Sprawdź swoją skrzynkę email i czekaj na zatwierdzenie przez administratora.')
            ->with('dl_event', $dlEvent);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
