<?php

namespace App\Livewire;

use App\Models\Rating;
use App\Models\User;
use App\Models\Announcement;
use App\Services\ProfanityFilter;
use App\Mail\NewRatingMail;
use App\Livewire\Concerns\TracksEvents;
use Livewire\Component;

class RateUser extends Component
{
    use TracksEvents;
    public User $ratedUser;
    public Announcement $announcement;
    public $rating = 5;
    public $comment = '';
    public $hasRated = false;

    public function mount(User $user, Announcement $announcement)
    {
        $this->ratedUser = $user;
        $this->announcement = $announcement;

        $this->hasRated = Rating::where('announcement_id', $announcement->id)
            ->where('rater_id', auth()->id())
            ->where('rated_id', $user->id)
            ->exists();
    }

    public function submit()
    {
        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        if (!$this->canRate()) {
            $this->dispatch('notify', message: 'Możesz wystawiać oceny tylko dla projektów, w których brałeś udział', type: 'error');
            return;
        }

        // $hasRated pochodzi od klienta - duplikat sprawdzamy w bazie.
        if (Rating::where('announcement_id', $this->announcement->id)
            ->where('rater_id', auth()->id())
            ->where('rated_id', $this->ratedUser->id)
            ->exists()) {
            $this->hasRated = true;
            $this->dispatch('notify', message: 'Ta osoba została już przez Ciebie oceniona w tym projekcie', type: 'error');
            return;
        }

        // Sprawdzenie wulgaryzmów
        if ($this->comment && ProfanityFilter::containsProfanity($this->comment)) {
            $badWords = ProfanityFilter::findProfanity($this->comment);
            $this->dispatch('notify',
                message: 'Twoja opinia zawiera niedozwolone słowa: ' . implode(', ', $badWords) . '. Zmień treść komentarza.',
                type: 'error'
            );
            return;
        }

        $rating = Rating::create([
            'announcement_id' => $this->announcement->id,
            'rater_id' => auth()->id(),
            'rated_id' => $this->ratedUser->id,
            'rating' => $this->rating,
            'comment' => $this->comment ?: null,
            'is_approved' => false, // Wymaga akceptacji admina
        ]);

        // Powiadomienie do wszystkich adminów
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            try {
                \Mail::to($admin->email)->send(new NewRatingMail($rating));
            } catch (\Exception $e) {
                \Log::warning('Failed to send rating notification email: ' . $e->getMessage());
            }
        }

        $this->hasRated = true;
        $this->trackEvent('rate_user');
        $this->dispatch('notify', message: 'Dziękujemy za wystawienie oceny! Zostanie ona wyświetlona po weryfikacji przez administratora.', type: 'success');
    }

    /**
     * Ocenić można wyłącznie drugą stronę współpracy: zleceniodawca ocenia
     * wykonawcę z zaakceptowaną ofertą, a wykonawca - zleceniodawcę.
     */
    private function canRate(): bool
    {
        $userId = auth()->id();

        if (!$userId || $userId === $this->ratedUser->id) {
            return false;
        }

        $acceptedFreelancer = fn (int $freelancerId) => $this->announcement->proposals()
            ->where('user_id', $freelancerId)
            ->where('status', 'accepted')
            ->exists();

        // Zleceniodawca ocenia wykonawcę
        if ($this->announcement->user_id === $userId) {
            return $acceptedFreelancer($this->ratedUser->id);
        }

        // Wykonawca ocenia zleceniodawcę
        return $this->announcement->user_id === $this->ratedUser->id
            && $acceptedFreelancer($userId);
    }

    // Metoda usunięta - średnia jest teraz liczona automatycznie przez model Rating

    public function render()
    {
        return view('livewire.rate-user');
    }
}

