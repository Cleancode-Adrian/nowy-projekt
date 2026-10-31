<?php

namespace App\Livewire;

use App\Models\Announcement;
use App\Models\Message;
use App\Models\PortfolioItem;
use App\Models\Rating;
use App\Models\Report;
use App\Models\User;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ReportContent extends Component
{
    /**
     * Typy, które wolno zgłosić. reportable_type trafia do relacji morphTo,
     * więc nie może być dowolną nazwą klasy przysłaną przez klienta.
     */
    private const REPORTABLE_TYPES = [
        Announcement::class,
        User::class,
        Message::class,
        Rating::class,
        PortfolioItem::class,
    ];

    #[Locked]
    public $reportableType;

    #[Locked]
    public $reportableId;

    public $reason = 'spam';
    public $description = '';
    public $submitted = false;

    public function submit()
    {
        if (!auth()->check()) {
            $this->dispatch('notify', message: 'Zaloguj się, aby zgłosić treść', type: 'error');
            return;
        }

        if (!in_array($this->reportableType, self::REPORTABLE_TYPES, true)
            || !$this->reportableType::whereKey($this->reportableId)->exists()) {
            $this->dispatch('notify', message: 'Nie można zgłosić tej treści', type: 'error');
            return;
        }

        $this->validate([
            'reason' => 'required|in:spam,inappropriate,fraud,other',
            'description' => 'nullable|string|max:500',
        ]);

        Report::create([
            'reporter_id' => auth()->id(),
            'reportable_type' => $this->reportableType,
            'reportable_id' => $this->reportableId,
            'reason' => $this->reason,
            'description' => $this->description,
        ]);

        $this->submitted = true;
        $this->dispatch('notify', message: 'Zgłoszenie wysłane. Dziękujemy!', type: 'success');
    }

    public function render()
    {
        return view('livewire.report-content');
    }
}
