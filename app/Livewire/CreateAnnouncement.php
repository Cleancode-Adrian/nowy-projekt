<?php

namespace App\Livewire;

use App\Livewire\Concerns\TracksEvents;
use App\Mail\NewAnnouncementMail;
use App\Models\Announcement;
use App\Models\AnnouncementAttachment;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Rules\SafeUpload;
use App\Services\ProfanityFilter;
use App\Services\UserRegistration;
use App\Support\DataLayer;
use App\Support\FbPixel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Formularz zlecenia. Zalogowany zleceniodawca publikuje od razu.
 * Gość (config security.guest_announcement_form) najpierw opisuje projekt
 * (krok 1), a w kroku 2 zakłada konto albo loguje się - i dopiero wtedy
 * zlecenie jest zapisywane i wysyłane do moderacji.
 */
class CreateAnnouncement extends Component
{
    use WithFileUploads;
    use TracksEvents;

    public $title = '';
    public $description = '';
    public $category_id = '';
    public $budget_min = '';
    public $budget_max = '';
    public $budget_currency = 'PLN';
    public $hourly_rate_min = '';
    public $hourly_rate_max = '';
    public $deadline = '';
    public $location = '';
    public $is_urgent = false;
    public $selectedTags = [];
    public $attachments = [];

    public $categories;
    public $tags;

    /** Krok formularza dla gościa: 1 = projekt, 2 = konto i publikacja. */
    public int $step = 1;

    /** 'register' - nowe konto, 'login' - mam już konto. */
    public string $authMode = 'register';

    public $reg_name = '';
    public $reg_email = '';
    public $reg_phone = '';
    public $reg_password = '';
    public $reg_password_confirmation = '';
    public $reg_accept_privacy = false;
    public $reg_accept_terms = false;

    public $login_email = '';
    public $login_password = '';

    /** Pułapka na boty - pole niewidoczne dla ludzi. */
    public $website = '';

    protected function rules()
    {
        return [
            'title' => 'required|min:10|max:255',
            'description' => 'required|min:50',
            'category_id' => 'required|exists:categories,id',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0|gte:budget_min',
            'hourly_rate_min' => 'nullable|numeric|min:0',
            'hourly_rate_max' => 'nullable|numeric|min:0|gte:hourly_rate_min',
            'deadline' => 'nullable|date|after:today',
            'location' => 'nullable|string|max:255',
            'budget_currency' => 'required|in:PLN,EUR,USD,GBP',
            'is_urgent' => 'nullable|boolean',
            'selectedTags' => 'array|max:10',
            'selectedTags.*' => 'exists:tags,id',
            'attachments.*' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp,zip,doc,docx', new SafeUpload(['pdf', 'jpg', 'jpeg', 'png', 'webp', 'zip', 'doc', 'docx'])],
        ];
    }

    protected $messages = [
        'title.required' => 'Tytuł jest wymagany',
        'title.min' => 'Tytuł musi mieć minimum 10 znaków',
        'description.required' => 'Opis jest wymagany',
        'description.min' => 'Opis musi mieć minimum 50 znaków',
        'category_id.required' => 'Wybierz kategorię',
        'budget_max.gte' => 'Budżet maksymalny musi być większy lub równy minimalnemu',
        'deadline.after' => 'Termin musi być w przyszłości',
    ];

    public function mount()
    {
        $this->categories = Category::where('is_active', true)->orderBy('order')->get();
        $this->tags = Tag::forAnnouncements()->orderBy('name')->get();

        if (!auth()->check() && !config('security.guest_announcement_form', true)) {
            return redirect()->route('login');
        }

        // Zlecenia publikują tylko zleceniodawcy (i admin). Wykonawca składa oferty.
        if (auth()->check() && !$this->canPublish()) {
            session()->flash('error', 'Zlecenia mogą dodawać tylko zleceniodawcy. Jako wykonawca możesz przeglądać zlecenia i składać oferty.');
            return redirect()->route('announcements.index');
        }

        if ($this->categories->isEmpty()) {
            session()->flash('error', 'Brak aktywnych kategorii. Skontaktuj się z administratorem.');
            return redirect()->route(auth()->check() ? 'dashboard' : 'home');
        }
    }

    private function canPublish(?User $user = null): bool
    {
        $user ??= auth()->user();

        return $user && ($user->isClient() || $user->isAdmin());
    }

    public function isGuest(): bool
    {
        return !auth()->check();
    }

    /** Krok 1 -> 2 (gość) albo od razu publikacja (zalogowany). */
    public function next()
    {
        if (!$this->isGuest()) {
            return $this->submit();
        }

        $this->validateProject();

        $this->step = 2;
        $this->resetErrorBag();

        // Ściana konta w formularzu - ile osób z reklam dochodzi do tego kroku.
        $this->trackEvent('login_wall_view', ['link_location' => 'announcement_create']);
    }

    public function back(): void
    {
        $this->step = 1;
    }

    public function setAuthMode(string $mode): void
    {
        $this->authMode = $mode === 'login' ? 'login' : 'register';
        $this->resetErrorBag();
    }

    public function submit()
    {
        if ($this->isGuest() && !config('security.guest_announcement_form', true)) {
            abort(403);
        }

        // Ponowne sprawdzenie przy zapisie - żądanie Livewire można wysłać
        // z pominięciem mount().
        if (!$this->isGuest()) {
            abort_unless($this->canPublish(), 403, 'Zlecenia mogą dodawać tylko zleceniodawcy.');
        }

        try {
            $this->validateProject();
        } catch (ValidationException $e) {
            $this->step = 1;
            throw $e;
        }

        $user = auth()->user();

        if (!$user) {
            $user = $this->authMode === 'login' ? $this->loginExistingUser() : $this->registerNewUser();
            if (!$user) {
                return null;
            }
        }

        $announcement = $this->createAnnouncement($user);
        $this->trackLead($announcement, $user);

        // Szkic z localStorage nie jest już potrzebny - layout go usunie.
        session()->flash('clear_announcement_draft', true);

        if (!auth()->check()) {
            // Konto czeka na akceptację (AUTO_APPROVE_CLIENTS=false) - zlecenie
            // jest zapisane, ale użytkownik nie może się jeszcze zalogować.
            session()->flash('success', 'Konto i zlecenie zapisane. Zlecenie trafi do wykonawców po weryfikacji konta przez administratora - dostaniesz e-mail.');
            return redirect()->route('login');
        }

        session()->flash('success', 'Ogłoszenie zostało dodane pomyślnie! Zostanie opublikowane po weryfikacji przez administratora.');

        return redirect()->route('dashboard');
    }

    /** Walidacja opisu projektu + filtr wulgaryzmów. */
    private function validateProject(): void
    {
        $this->validate();

        if (ProfanityFilter::containsProfanity($this->title) || ProfanityFilter::containsProfanity($this->description)) {
            $badWords = array_unique(array_merge(
                ProfanityFilter::findProfanity($this->title),
                ProfanityFilter::findProfanity($this->description)
            ));

            throw ValidationException::withMessages([
                'description' => 'Twoje ogłoszenie zawiera niedozwolone słowa: ' . implode(', ', $badWords) . '. Zmień treść ogłoszenia.',
            ]);
        }
    }

    private function registerNewUser(): ?User
    {
        if (filled($this->website)) {
            // Bot wypełnił ukryte pole - udajemy zwykły błąd, nic nie zapisujemy.
            throw ValidationException::withMessages(['reg_email' => 'Nie udało się założyć konta. Spróbuj ponownie.']);
        }

        $key = 'register-inline|' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'reg_email' => 'Za dużo prób. Spróbuj ponownie za ' . RateLimiter::availableIn($key) . ' s.',
            ]);
        }
        RateLimiter::hit($key, 60);

        $rules = UserRegistration::rules('reg_', false);
        unset($rules['reg_company']); // formularz zlecenia nie pyta o firmę

        $data = $this->validate($rules, UserRegistration::messages('reg_'));

        try {
            $user = app(UserRegistration::class)->register([
                'name' => $data['reg_name'],
                'email' => $data['reg_email'],
                'password' => $data['reg_password'],
                'phone' => $data['reg_phone'] ?: null,
                'role' => 'client',
            ]);
        } catch (\Throwable $e) {
            Log::error('Inline registration failed: ' . $e->getMessage());
            throw ValidationException::withMessages([
                'reg_email' => 'Wystąpił błąd podczas zakładania konta. Spróbuj ponownie lub napisz: biuro@cleancodeas.pl',
            ]);
        }

        DataLayer::flash(['event' => 'sign_up', 'method' => 'email', 'user_role' => 'klient']);
        FbPixel::flash('CompleteRegistration', ['status' => true, 'content_name' => 'klient'], $user);

        if ($user->is_approved) {
            Auth::login($user);
            session()->regenerate();
        }

        return $user;
    }

    private function loginExistingUser(): ?User
    {
        $this->validate(
            ['login_email' => 'required|email', 'login_password' => 'required'],
            ['login_email.required' => 'Podaj adres e-mail', 'login_password.required' => 'Podaj hasło'],
        );

        $key = 'login-inline|' . mb_strtolower($this->login_email) . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            throw ValidationException::withMessages([
                'login_email' => 'Za dużo prób logowania. Spróbuj ponownie za ' . RateLimiter::availableIn($key) . ' s.',
            ]);
        }

        if (!Auth::attempt(['email' => $this->login_email, 'password' => $this->login_password], true)) {
            RateLimiter::hit($key, 60);
            throw ValidationException::withMessages(['login_email' => 'Nieprawidłowy e-mail lub hasło.']);
        }

        RateLimiter::clear($key);
        $user = Auth::user();

        if (!$user->is_approved) {
            Auth::logout();
            throw ValidationException::withMessages(['login_email' => 'Twoje konto oczekuje na zatwierdzenie przez administratora.']);
        }

        if (!$this->canPublish($user)) {
            Auth::logout();
            throw ValidationException::withMessages([
                'login_email' => 'To konto wykonawcy - zlecenia mogą dodawać tylko zleceniodawcy. Załóż konto zleceniodawcy na inny e-mail.',
            ]);
        }

        session()->regenerate();
        DataLayer::flash(['event' => 'login', 'method' => 'email']);

        return $user;
    }

    private function createAnnouncement(User $user): Announcement
    {
        $announcement = Announcement::create([
            'user_id' => $user->id,
            'title' => $this->title,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'budget_min' => $this->budget_min ?: null,
            'budget_max' => $this->budget_max ?: null,
            'budget_currency' => $this->budget_currency,
            'hourly_rate_min' => $this->hourly_rate_min ?: null,
            'hourly_rate_max' => $this->hourly_rate_max ?: null,
            'deadline' => $this->deadline ?: null,
            'location' => $this->location ?: null,
            'is_urgent' => $this->is_urgent,
            'status' => 'pending',
            'is_approved' => false,
            // Do późniejszych zdarzeń z serwera (qualify_lead po akceptacji).
            'ga_client_id' => \App\Support\Ga4MeasurementProtocol::currentClientId(),
        ]);

        if (!empty($this->selectedTags)) {
            $announcement->tags()->sync($this->selectedTags);
        }

        foreach ($this->attachments ?: [] as $file) {
            $path = $file->store('attachments', 'public');

            AnnouncementAttachment::create([
                'announcement_id' => $announcement->id,
                'filename' => basename($path),
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        foreach (User::where('role', 'admin')->get() as $admin) {
            try {
                Mail::to($admin->email)->send(new NewAnnouncementMail($announcement));
            } catch (\Throwable $e) {
                Log::warning('Failed to send announcement notification email: ' . $e->getMessage());
            }
        }

        return $announcement;
    }

    /**
     * Jedno zdarzenie w trzy miejsca: Meta Pixel (Lead), Meta CAPI (Lead, ten
     * sam event_id = deduplikacja) i dataLayer (generate_lead -> GTM -> GA4).
     * Idą flashem, bo po zapisie jest redirect.
     */
    private function trackLead(Announcement $announcement, User $user): void
    {
        // Jeden Lead na zlecenie: flagę ustawiamy atomowo, więc nawet podwójne
        // wywołanie nie wyśle drugiego zdarzenia.
        $claimed = Announcement::whereKey($announcement->id)
            ->whereNull('lead_tracked_at')
            ->update(['lead_tracked_at' => now()]);

        if (!$claimed) {
            return;
        }

        $categorySlug = $this->categories->firstWhere('id', (int) $announcement->category_id)?->slug
            ?? $announcement->category?->slug;

        FbPixel::flash('Lead', array_filter([
            'content_name' => 'Zlecenie',
            'content_category' => $categorySlug,
        ]), $user, 'lead-' . $announcement->id);

        DataLayer::flash(array_filter([
            'event' => 'generate_lead',
            'lead_type' => 'zlecenie',
            'item_category' => $categorySlug,
            'value' => 0,
            'currency' => 'PLN',
        ], fn ($v) => $v !== null));
    }

    public function render()
    {
        return view('livewire.create-announcement')->layout('layouts.app', [
            'title' => 'Dodaj zlecenie - Projekciarz.pl',
            'description' => 'Opisz projekt strony, sklepu lub aplikacji i porównaj oferty sprawdzonych wykonawców. Publikacja jest bezpłatna.',
        ]);
    }
}
