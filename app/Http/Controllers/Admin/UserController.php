<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required|in:admin,client,freelancer',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|max:2048',
            'website' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
            'skills' => 'nullable|array',
            'experience_level' => 'nullable|in:junior,mid,senior,expert',
            'is_verified' => 'boolean',
        ], [
            'name.required' => 'Nazwa jest wymagana',
            'email.required' => 'Email jest wymagany',
            'email.unique' => 'Ten email jest już zajęty',
            'password.required' => 'Hasło jest wymagane',
            'password.min' => 'Hasło musi mieć minimum 8 znaków',
            'avatar.image' => 'Plik musi być obrazem',
            'avatar.max' => 'Avatar może mieć maksymalnie 2MB',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'company' => $validated['company'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'website' => $validated['website'] ?? null,
            'linkedin_url' => $validated['linkedin_url'] ?? null,
            'github_url' => $validated['github_url'] ?? null,
            'skills' => $validated['skills'] ?? null,
            'experience_level' => $validated['experience_level'] ?? null,
            'is_verified' => $request->has('is_verified'),
            'email_verified_at' => $request->has('is_verified') ? now() : null,
        ];

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'Użytkownik został dodany pomyślnie!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8',
            'role' => 'required|in:admin,client,freelancer',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|max:2048',
            'website' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
            'skills' => 'nullable|array',
            'experience_level' => 'nullable|in:junior,mid,senior,expert',
            'is_verified' => 'boolean',
        ], [
            'name.required' => 'Nazwa jest wymagana',
            'email.required' => 'Email jest wymagany',
            'email.unique' => 'Ten email jest już zajęty',
            'password.min' => 'Hasło musi mieć minimum 8 znaków',
            'avatar.image' => 'Plik musi być obrazem',
            'avatar.max' => 'Avatar może mieć maksymalnie 2MB',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'company' => $validated['company'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'website' => $validated['website'] ?? null,
            'linkedin_url' => $validated['linkedin_url'] ?? null,
            'github_url' => $validated['github_url'] ?? null,
            'skills' => $validated['skills'] ?? null,
            'experience_level' => $validated['experience_level'] ?? null,
            'is_verified' => $request->has('is_verified'),
        ];

        if ($request->has('is_verified') && !$user->email_verified_at) {
            $data['email_verified_at'] = now();
        } elseif (!$request->has('is_verified')) {
            $data['email_verified_at'] = null;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Użytkownik został zaktualizowany!');
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Nie możesz usunąć własnego konta!');
        }

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Użytkownik został usunięty!');
    }
}

