<?php

namespace App\Livewire;

use App\Models\PortfolioItem;
use Livewire\Component;
use Livewire\WithFileUploads;

class Portfolio extends Component
{
    use WithFileUploads;

    public $portfolioItems;
    public $showForm = false;
    public $editingId = null;

    public $title = '';
    public $description = '';
    public $url = '';
    public $image;
    public $technologies = '';
    public $completed_at = '';
    public $is_featured = false;

    protected function rules()
    {
        return [
            'title' => 'required|min:5|max:255',
            'description' => 'required|min:20',
            'url' => 'nullable|url',
            'image' => 'nullable|image|max:2048',
            'technologies' => 'nullable|string',
            'completed_at' => 'nullable|date',
        ];
    }

    public function mount()
    {
        $this->loadPortfolio();
    }

    public function loadPortfolio()
    {
        $this->portfolioItems = PortfolioItem::where('user_id', auth()->id())
            ->ordered()
            ->get();
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        if (!$this->showForm) {
            $this->resetForm();
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'user_id' => auth()->id(),
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url ?: null,
            'technologies' => $this->technologies ? explode(',', $this->technologies) : null,
            'completed_at' => $this->completed_at ?: null,
            'is_featured' => $this->is_featured,
        ];

        if ($this->image) {
            $data['image'] = $this->image->store('portfolio', 'public');
        }

        if ($this->editingId) {
            $item = PortfolioItem::findOrFail($this->editingId);
            if ($item->user_id !== auth()->id()) {
                $this->dispatch('notify', message: 'Brak uprawnień', type: 'error');
                return;
            }
            $item->update($data);
            $this->dispatch('notify', message: 'Portfolio zaktualizowane!', type: 'success');
        } else {
            PortfolioItem::create($data);
            $this->dispatch('notify', message: 'Dodano do portfolio!', type: 'success');
        }

        $this->resetForm();
        $this->loadPortfolio();
        $this->showForm = false;
    }

    public function edit($id)
    {
        $item = PortfolioItem::findOrFail($id);

        if ($item->user_id !== auth()->id()) {
            $this->dispatch('notify', message: 'Brak uprawnień', type: 'error');
            return;
        }

        $this->editingId = $id;
        $this->title = $item->title;
        $this->description = $item->description;
        $this->url = $item->url;
        $this->technologies = is_array($item->technologies) ? implode(',', $item->technologies) : '';
        $this->completed_at = $item->completed_at?->format('Y-m-d');
        $this->is_featured = $item->is_featured;
        $this->showForm = true;
    }

    public function delete($id)
    {
        $item = PortfolioItem::findOrFail($id);

        if ($item->user_id !== auth()->id()) {
            $this->dispatch('notify', message: 'Brak uprawnień', type: 'error');
            return;
        }

        if ($item->image) {
            \Storage::disk('public')->delete($item->image);
        }

        $item->delete();
        $this->loadPortfolio();
        $this->dispatch('notify', message: 'Usunięto z portfolio', type: 'info');
    }

    public function resetForm()
    {
        $this->reset(['title', 'description', 'url', 'image', 'technologies', 'completed_at', 'is_featured', 'editingId']);
    }

    public function render()
    {
        return view('livewire.portfolio')->layout('layouts.app', [
            'title' => 'Moje Portfolio - WebFreelance',
            'description' => 'Zarządzaj swoim portfolio projektów',
        ]);
    }
}

