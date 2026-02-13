<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\RecaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function sendResetLink(Request $request, RecaptchaService $recaptcha)
    {
        // Verify reCAPTCHA (v3). In local/testing it is allowed to be unconfigured.
        $recaptchaToken = (string) $request->input('g-recaptcha-response', '');
        $minScore = (float) config('services.recaptcha.min_score_login', 0.7);
        if (!$recaptcha->verify($recaptchaToken, $minScore, 'forgot_password')) {
            return back()->withErrors([
                'email' => 'Weryfikacja bezpieczeństwa (reCAPTCHA) nie powiodła się. Odśwież stronę i spróbuj ponownie.',
            ]);
        }

        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function reset(Request $request, RecaptchaService $recaptcha)
    {
        // Verify reCAPTCHA (v3). In local/testing it is allowed to be unconfigured.
        $recaptchaToken = (string) $request->input('g-recaptcha-response', '');
        $minScore = (float) config('services.recaptcha.min_score_login', 0.7);
        if (!$recaptcha->verify($recaptchaToken, $minScore, 'reset_password')) {
            return back()->withErrors([
                'email' => 'Weryfikacja bezpieczeństwa (reCAPTCHA) nie powiodła się. Odśwież stronę i spróbuj ponownie.',
            ]);
        }

        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}

