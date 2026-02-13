<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\RecaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request, RecaptchaService $recaptcha)
    {
        $recaptchaToken = (string) $request->input('g-recaptcha-response', '');
        $minScore = (float) config('services.recaptcha.min_score_admin_login', 0.8);
        if (!$recaptcha->verify($recaptchaToken, $minScore, 'admin_login')) {
            return back()->withErrors([
                'email' => 'Weryfikacja bezpieczeństwa (reCAPTCHA) nie powiodła się. Odśwież stronę i spróbuj ponownie.',
            ])->onlyInput('email');
        }

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            if (!Auth::user()->isAdmin()) {
                Auth::logout();
                return back()->withErrors(['email' => 'Brak dostępu do panelu admina.']);
            }

            if (!Auth::user()->is_approved) {
                Auth::logout();
                return back()->withErrors(['email' => 'Konto oczekuje na zatwierdzenie.']);
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Nieprawidłowe dane logowania.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}

