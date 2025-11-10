<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileEdit extends Component
{
    use WithFileUploads;

    // Dane profilu
    public $name;
    public $email;
    public $phone;
    public $company;
    public $bio;
    public $website;
    public $linkedin_url;
    public $github_url;

    // Freelancer specific
    public $skills = [];
    public $experience_level;
    public $selectedSkills = '';

    // Avatar
    public $avatar;
    public $currentAvatar;

    // Zmiana hasła
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->company = $user->company;
        $this->bio = $user->bio;
        $this->website = $user->website;
        $this->linkedin_url = $user->linkedin_url;
        $this->github_url = $user->github_url;
        $this->currentAvatar = $user->avatar;

        if ($user->isFreelancer()) {
            $this->skills = $user->skills ?? [];
            $this->selectedSkills = is_array($user->skills) ? implode(', ', $user->skills) : '';
            $this->experience_level = $user->experience_level;
        }
    }

    public function updateProfile()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
        ];

        if (Auth::user()->isFreelancer()) {
            $rules['experience_level'] = 'nullable|in:junior,mid,senior';
        }

        $validated = $this->validate($rules);

        $user = Auth::user();

        if ($user->isFreelancer() && $this->selectedSkills) {
            $skillsArray = array_map('trim', explode(',', $this->selectedSkills));
            $validated['skills'] = array_filter($skillsArray);
        }

        $user->update($validated);

        $this->dispatch('notify',
            message: 'Profil zaktualizowany pomyślnie!',
            type: 'success'
        );
    }

    public function updateAvatar()
    {
        $this->validate([
            'avatar' => 'required|image|max:2048', // 2MB max
        ]);

        $user = Auth::user();

        // Usuń stary avatar
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Zapisz nowy avatar
        $path = $this->avatar->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        $this->currentAvatar = $path;
        $this->avatar = null;

        $this->dispatch('notify',
            message: 'Avatar zaktualizowany!',
            type: 'success'
        );
    }

    public function removeAvatar()
    {
        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
            $this->currentAvatar = null;

            $this->dispatch('notify',
                message: 'Avatar usunięty!',
                type: 'success'
            );
        }
    }

    public function changePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        // Sprawdź obecne hasło
        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Obecne hasło jest nieprawidłowe.');
            return;
        }

        // Zaktualizuj hasło
        $user->update([
            'password' => Hash::make($this->new_password),
        ]);

        // Wyczyść pola
        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';

        $this->dispatch('notify',
            message: 'Hasło zmienione pomyślnie!',
            type: 'success'
        );
    }

    public function render()
    {
        return view('livewire.profile-edit')
            ->layout('layouts.app', [
                'title' => 'Edycja profilu - WebFreelance',
            ]);
    }
}
