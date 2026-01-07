<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\NewUserRegisteredMail;
use App\Mail\UserRegisteredMail;
use App\Services\RecaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function login(Request $request, RecaptchaService $recaptcha)
    {
        // Verify reCAPTCHA only if configured
        if (config('services.recaptcha.secret_key')) {
            $recaptchaToken = $request->input('g-recaptcha-response');
            if (!$recaptcha->verify($recaptchaToken, 0.5)) {
                return back()->withErrors([
                    'email' => 'Weryfikacja reCAPTCHA nie powiodła się. Spróbuj ponownie.',
                ])->onlyInput('email');
            }
        }

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

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Nieprawidłowe dane logowania.',
        ])->onlyInput('email');
    }

    public function register(Request $request, RecaptchaService $recaptcha)
    {
        try {
            // Verify reCAPTCHA only if configured
            if (config('services.recaptcha.secret_key')) {
                $recaptchaToken = $request->input('g-recaptcha-response');
                if (!$recaptcha->verify($recaptchaToken, 0.5)) {
                    return back()->withErrors([
                        'email' => 'Weryfikacja reCAPTCHA nie powiodła się. Spróbuj ponownie.',
                    ])->withInput();
                }
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => ['required', 'confirmed', Password::min(8)],
                'phone' => 'nullable|string|max:20',
                'company' => 'nullable|string|max:255',
                'role' => 'required|in:client,freelancer',
                'accept_privacy' => 'required|accepted',
                'accept_terms' => 'required|accepted',
            ], [
                'accept_privacy.accepted' => 'Musisz zaakceptować politykę prywatności',
                'accept_terms.accepted' => 'Musisz zaakceptować regulamin',
            ]);

            // Use database transaction to ensure data integrity
            $user = DB::transaction(function () use ($validated) {
                return User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'phone' => $validated['phone'] ?? null,
                    'company' => $validated['company'] ?? null,
                    'role' => $validated['role'],
                    'is_approved' => false, // require admin approval
                ]);
            });

            // Email do użytkownika - potwierdzenie rejestracji
            try {
                Mail::to($user->email)->send(new UserRegisteredMail($user));
            } catch (\Exception $e) {
                Log::warning('Failed to send user registration email: ' . $e->getMessage(), [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'exception' => $e->getTraceAsString()
                ]);
            }

            // Powiadomienie do wszystkich adminów
            try {
                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    try {
                        Mail::to($admin->email)->send(new NewUserRegisteredMail($user));
                    } catch (\Exception $e) {
                        Log::warning('Failed to send user registration notification email to admin: ' . $e->getMessage(), [
                            'admin_id' => $admin->id,
                            'user_id' => $user->id,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to fetch admins for notification: ' . $e->getMessage());
            }

            return redirect()->route('login')->with('success', 'Konto utworzone! Sprawdź swoją skrzynkę email i czekaj na zatwierdzenie przez administratora.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exceptions to show proper error messages
            throw $e;
        } catch (\Exception $e) {
            // Log the full error for debugging
            Log::error('User registration failed: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
                'request_data' => $request->except(['password', 'password_confirmation']),
            ]);

            return back()->withErrors([
                'email' => 'Wystąpił błąd podczas rejestracji. Spróbuj ponownie lub skontaktuj się z nami: biuro@cleancodeas.pl',
            ])->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}

