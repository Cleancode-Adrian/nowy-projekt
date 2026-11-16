<?php

namespace App\Services;

use App\Mail\NewUserRegisteredMail;
use App\Mail\UserRegisteredMail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

/**
 * Zakładanie konta - wspólne dla strony /register i dla formularza zlecenia,
 * w którym gość zakłada konto w ostatnim kroku.
 */
class UserRegistration
{
    /** Litery (także z akcentami), spacje, apostrof, kropka, myślnik. */
    public const NAME_PATTERN = "/^[\pL\pM][\pL\pM\s'.\-]*$/u";

    /** Adres strony w treści pola (http://, www., domena/ścieżka). */
    public const LINK_PATTERN = '/(:\/\/|www\.|[a-z0-9-]+\.[a-z]{2,}\/)/i';

    /** Reguły walidacji pól konta. $prefix pozwala użyć ich w Livewire (np. "reg_"). */
    public static function rules(string $prefix = '', bool $withRole = true): array
    {
        $rules = [
            // Same litery: boty wpisują w imię reklamę z linkiem, a my odsyłamy
            // ją w mailu powitalnym na cudzy adres (spam z naszej domeny).
            $prefix . 'name' => ['required', 'string', 'max:100', 'regex:' . static::NAME_PATTERN],
            $prefix . 'email' => 'required|email|max:255|unique:users,email',
            $prefix . 'password' => ['required', 'confirmed', Password::min(8)],
            $prefix . 'phone' => 'nullable|string|max:20',
            $prefix . 'company' => ['nullable', 'string', 'max:255', 'not_regex:' . static::LINK_PATTERN],
            $prefix . 'accept_privacy' => 'required|accepted',
            $prefix . 'accept_terms' => 'required|accepted',
        ];

        if ($withRole) {
            $rules[$prefix . 'role'] = 'required|in:client,freelancer';
        }

        return $rules;
    }

    public static function messages(string $prefix = ''): array
    {
        return [
            $prefix . 'name.regex' => 'Imię i nazwisko może zawierać tylko litery, spacje i myślniki.',
            $prefix . 'name.max' => 'Imię i nazwisko może mieć maksymalnie 100 znaków.',
            $prefix . 'company.not_regex' => 'Nazwa firmy nie może zawierać linków.',
            $prefix . 'accept_privacy.accepted' => 'Musisz zaakceptować politykę prywatności',
            $prefix . 'accept_privacy.required' => 'Musisz zaakceptować politykę prywatności',
            $prefix . 'accept_terms.accepted' => 'Musisz zaakceptować regulamin',
            $prefix . 'accept_terms.required' => 'Musisz zaakceptować regulamin',
            $prefix . 'email.unique' => 'Konto z tym adresem e-mail już istnieje. Zaloguj się.',
            $prefix . 'password.confirmed' => 'Hasła nie są takie same.',
            $prefix . 'password.min' => 'Hasło musi mieć co najmniej 8 znaków.',
        ];
    }

    /** Reguła tokenu Turnstile - pusta, gdy Turnstile jest wyłączony. */
    public static function captchaRules(): array
    {
        if (!Turnstile::enabled()) {
            return [];
        }

        return [Turnstile::FIELD => ['required', 'string', function (string $attribute, mixed $value, \Closure $fail) {
            if (!app(Turnstile::class)->verify($value, request()->ip())) {
                $fail('Weryfikacja antyspamowa nie powiodła się. Odśwież stronę i spróbuj ponownie.');
            }
        }]];
    }

    public static function captchaMessages(): array
    {
        return [
            Turnstile::FIELD . '.required' => 'Poczekaj chwilę, aż zakończy się weryfikacja antyspamowa (pole nad przyciskiem), i wyślij formularz ponownie.',
        ];
    }

    /**
     * Zleceniodawcy wchodzą od razu - jedyne, co mogą zrobić, to opublikować
     * zlecenie, a te i tak przechodzą moderację. Wykonawców zatwierdza admin.
     */
    public static function autoApproves(string $role): bool
    {
        return $role === 'client' && config('security.auto_approve_clients', true);
    }

    /**
     * @param array{name: string, email: string, password: string, role: string, phone?: ?string, company?: ?string} $data
     */
    public function register(array $data): User
    {
        $user = DB::transaction(fn () => User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'company' => $data['company'] ?? null,
            'role' => $data['role'],
            'is_approved' => static::autoApproves($data['role']),
        ]));

        $this->sendNotifications($user);

        return $user;
    }

    private function sendNotifications(User $user): void
    {
        // Link weryfikacyjny tylko, gdy weryfikacja jest egzekwowana.
        if (config('security.require_email_verification')) {
            try {
                $user->sendEmailVerificationNotification();
            } catch (\Throwable $e) {
                Log::warning('Failed to send email verification link: ' . $e->getMessage(), ['user_id' => $user->id]);
            }
        }

        try {
            Mail::to($user->email)->send(new UserRegisteredMail($user));
        } catch (\Throwable $e) {
            Log::warning('Failed to send user registration email: ' . $e->getMessage(), ['user_id' => $user->id]);
        }

        try {
            foreach (User::where('role', 'admin')->get() as $admin) {
                try {
                    Mail::to($admin->email)->send(new NewUserRegisteredMail($user));
                } catch (\Throwable $e) {
                    Log::warning('Failed to notify admin about registration: ' . $e->getMessage(), [
                        'admin_id' => $admin->id,
                        'user_id' => $user->id,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to fetch admins for notification: ' . $e->getMessage());
        }
    }
}
