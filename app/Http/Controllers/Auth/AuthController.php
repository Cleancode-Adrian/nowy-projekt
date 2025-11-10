<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\NewUserRegisteredMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

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

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Nieprawidłowe dane logowania.',
        ])->onlyInput('email');
    }

    public function register(Request $request)
    {
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

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'company' => $validated['company'] ?? null,
            'role' => $validated['role'],
            'is_approved' => false, // require admin approval
        ]);

        // Powiadomienie do wszystkich adminów
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(new NewUserRegisteredMail($user));
            } catch (\Exception $e) {
                \Log::warning('Failed to send user registration notification email: ' . $e->getMessage());
            }
        }

        return redirect()->route('login')->with('success', 'Konto utworzone! Oczekuje na zatwierdzenie przez administratora.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}

