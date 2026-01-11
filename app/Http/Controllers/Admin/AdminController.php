<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Announcement;
use App\Models\Rating;
use App\Mail\UserApprovedMail;
use App\Mail\AnnouncementApprovedMail;
use App\Mail\NewAnnouncementForFreelancersMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Brak dostępu');
        }

        $stats = [
            'total_users' => User::count(),
            'pending_users' => User::where('is_approved', false)->count(),
            'total_announcements' => Announcement::count(),
            'pending_announcements' => Announcement::where('is_approved', false)->count(),
            'total_ratings' => Rating::count(),
            'pending_ratings' => Rating::where('is_approved', false)->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function users()
    {
        $users = User::latest()->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function viewUser($id)
    {
        $user = User::with('announcements')->findOrFail($id);
        return view('admin.view-user', compact('user'));
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Don't delete yourself
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Nie możesz usunąć samego siebie!');
        }

        // Don't delete other admins
        if ($user->isAdmin()) {
            return redirect()->back()->with('error', 'Nie możesz usunąć administratora!');
        }

        $user->delete();
        return redirect()->route('admin.users')->with('success', 'Użytkownik usunięty');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.edit-user', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'role' => 'required|in:client,freelancer,admin',
            'is_approved' => 'boolean',
        ]);

        $user->update($validated);
        return redirect()->route('admin.users.view', $user->id)->with('success', 'Użytkownik zaktualizowany');
    }

    public function changeUserPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user->update([
            'password' => \Hash::make($validated['new_password']),
        ]);

        return redirect()->back()->with('success', 'Hasło użytkownika zmienione');
    }

    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_approved' => true]);

        // Send email notification
        try {
            Mail::to($user->email)->send(new UserApprovedMail($user));
        } catch (\Exception $e) {
            \Log::error('Failed to send approval email: ' . $e->getMessage());
        }

        return back()->with('success', 'Użytkownik zatwierdzony!');
    }

    public function announcements()
    {
        $announcements = Announcement::with(['user', 'category'])->latest()->paginate(20);
        return view('admin.announcements', compact('announcements'));
    }

    public function editAnnouncement($id)
    {
        $announcement = Announcement::with(['user', 'category'])->findOrFail($id);
        return view('admin.edit-announcement', compact('announcement'));
    }

    public function updateAnnouncement(Request $request, $id)
    {
        $announcement = Announcement::with(['user', 'category', 'tags'])->findOrFail($id);
        $wasPublished = $announcement->status === 'published' && $announcement->is_approved;

        $status = $request->status;

        // Jeśli status to "closed" lub "published", ogłoszenie MUSI być zatwierdzone i widoczne
        // Jeśli status to "rejected", ogłoszenie nie może być zatwierdzone
        if ($status === 'closed' || $status === 'published') {
            $isApproved = true; // Zamknięte i opublikowane muszą być zatwierdzone
        } elseif ($status === 'rejected') {
            $isApproved = false; // Odrzucone nie mogą być zatwierdzone
        } else {
            // Dla innych statusów (pending, draft) używamy wartości z checkboxa
            $isApproved = $request->has('is_approved');
        }

        $announcement->update([
            'is_approved' => $isApproved,
            'status' => $status,
            'is_urgent' => $request->has('is_urgent'),
            'rejection_reason' => $status === 'rejected' ? $request->rejection_reason : null,
            'approved_at' => $isApproved ? ($announcement->approved_at ?? now()) : null,
        ]);

        // Send notifications if announcement is being published for the first time
        if ($status === 'published' && $isApproved && !$wasPublished) {
            // Send email notification to client
            try {
                Mail::to($announcement->user->email)->send(new AnnouncementApprovedMail($announcement));
            } catch (\Exception $e) {
                \Log::error('Failed to send announcement approval email: ' . $e->getMessage());
            }

            // Send email notification to all approved freelancers
            try {
                $freelancers = User::where('role', 'freelancer')
                    ->where('is_approved', true)
                    ->where('email_verified_at', '!=', null)
                    ->get();

                foreach ($freelancers as $freelancer) {
                    try {
                        Mail::to($freelancer->email)->send(new NewAnnouncementForFreelancersMail($announcement));
                    } catch (\Exception $e) {
                        \Log::warning('Failed to send announcement email to freelancer ' . $freelancer->email . ': ' . $e->getMessage());
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send announcement notifications to freelancers: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.announcements')->with('success', 'Ogłoszenie zaktualizowane!');
    }

    public function approveAnnouncement($id)
    {
        $announcement = Announcement::with(['user', 'category', 'tags'])->findOrFail($id);
        $announcement->update([
            'is_approved' => true,
            'status' => 'published',
            'approved_at' => now(),
        ]);

        // Send email notification to client
        try {
            Mail::to($announcement->user->email)->send(new AnnouncementApprovedMail($announcement));
        } catch (\Exception $e) {
            \Log::error('Failed to send announcement approval email: ' . $e->getMessage());
        }

        // Send email notification to all approved freelancers
        try {
            $freelancers = User::where('role', 'freelancer')
                ->where('is_approved', true)
                ->where('email_verified_at', '!=', null)
                ->get();

            foreach ($freelancers as $freelancer) {
                try {
                    Mail::to($freelancer->email)->send(new NewAnnouncementForFreelancersMail($announcement));
                } catch (\Exception $e) {
                    \Log::warning('Failed to send announcement email to freelancer ' . $freelancer->email . ': ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send announcement notifications to freelancers: ' . $e->getMessage());
        }

        return back()->with('success', 'Ogłoszenie zatwierdzone i opublikowane! Powiadomienia wysłane do freelancerów.');
    }

    public function rejectAnnouncement(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->update([
            'is_approved' => false,
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Ogłoszenie odrzucone!');
    }
}

