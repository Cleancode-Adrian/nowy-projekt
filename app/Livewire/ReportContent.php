<?php

namespace App\Livewire;

use App\Models\Report;
use Livewire\Component;

class ReportContent extends Component
{
    public $reportableType;
    public $reportableId;
    public $reason = 'spam';
    public $description = '';
    public $submitted = false;

    public function submit()
    {
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

