<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    public function index()
    {
        $pending = User::where('verification_status', 'pending')
            ->latest('updated_at')
            ->paginate(20);

        return view('admin.verifications.index', compact('pending'));
    }

    public function document($id): Response
    {
        $user = User::findOrFail($id);

        if (!$user->verification_document || !Storage::disk('local')->exists($user->verification_document)) {
            abort(404);
        }

        return Storage::disk('local')->response($user->verification_document);
    }

    public function approve($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verification_status' => 'approved',
            'verification_note' => null,
        ]);

        return redirect()->back()->with('success', "Konto {$user->name} zostało zweryfikowane!");
    }

    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'note' => 'nullable|string|max:255',
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'is_verified' => false,
            'verification_status' => 'rejected',
            'verification_note' => $validated['note'] ?? 'Dokument nieczytelny lub niewystarczający.',
        ]);

        return redirect()->back()->with('success', "Wniosek {$user->name} został odrzucony.");
    }
}
