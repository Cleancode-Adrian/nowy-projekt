<?php

namespace App\Livewire;

use App\Models\Announcement;
use App\Models\AnnouncementAttachment;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Mail\NewAnnouncementMail;
use App\Services\ProfanityFilter;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateAnnouncement extends Component
{
    use WithFileUploads;

    public $title = '';
    public $description = '';
    public $category_id = '';
    public $budget_min = '';
    public $budget_max = '';
    public $budget_currency = 'PLN';
    public $deadline = '';
    public $location = '';
    public $is_urgent = false;
    public $selectedTags = [];
    public $attachments = [];

    public $categories;
    public $tags;

    protected function rules()
    {
        return [
            'title' => 'required|min:10|max:255',
            'description' => 'required|min:50',
            'category_id' => 'required|exists:categories,id',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0|gte:budget_min',
            'deadline' => 'nullable|date|after:today',
            'location' => 'nullable|string|max:255',
            'selectedTags' => 'array|max:10',
            'selectedTags.*' => 'exists:tags,id',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,zip,doc,docx',
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
    }

    public function submit()
    {
        $this->validate();

        // Sprawdzenie wulgaryzmów w tytule i opisie
        if (ProfanityFilter::containsProfanity($this->title) || ProfanityFilter::containsProfanity($this->description)) {
            $badWords = array_unique(array_merge(
                ProfanityFilter::findProfanity($this->title),
                ProfanityFilter::findProfanity($this->description)
            ));
            session()->flash('error', 'Twoje ogłoszenie zawiera niedozwolone słowa: ' . implode(', ', $badWords) . '. Zmień treść ogłoszenia.');
            return;
        }

        $announcement = Announcement::create([
            'user_id' => auth()->id(),
            'title' => $this->title,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'budget_min' => $this->budget_min ?: null,
            'budget_max' => $this->budget_max ?: null,
            'budget_currency' => $this->budget_currency,
            'deadline' => $this->deadline ?: null,
            'location' => $this->location ?: null,
            'is_urgent' => $this->is_urgent,
            'status' => 'pending',
            'is_approved' => false,
        ]);

        if (!empty($this->selectedTags)) {
            $announcement->tags()->sync($this->selectedTags);
        }

        // Upload attachments
        if (!empty($this->attachments)) {
            foreach ($this->attachments as $file) {
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
        }

        // Powiadomienie do wszystkich adminów
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            try {
                \Mail::to($admin->email)->send(new NewAnnouncementMail($announcement));
            } catch (\Exception $e) {
                \Log::warning('Failed to send announcement notification email: ' . $e->getMessage());
            }
        }

        session()->flash('success', 'Ogłoszenie zostało dodane pomyślnie! Zostanie opublikowane po weryfikacji przez administratora.');

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.create-announcement')->layout('layouts.app', [
            'title' => 'Dodaj ogłoszenie - Projekciarz.pl',
            'description' => 'Utwórz nowe ogłoszenie i znajdź idealnego freelancera',
        ]);
    }
}

