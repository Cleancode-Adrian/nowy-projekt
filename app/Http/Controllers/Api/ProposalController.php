<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProposalController extends Controller
{
    /**
     * Get proposals for an announcement (for announcement owner)
     */
    public function index(Request $request, $announcementId): JsonResponse
    {
        $announcement = Announcement::findOrFail($announcementId);

        // Only announcement owner can see proposals
        if ($announcement->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $proposals = Proposal::where('announcement_id', $announcementId)
            ->with('freelancer')
            ->latest()
            ->get();

        return response()->json($proposals);
    }

    /**
     * Get my proposals (as freelancer)
     */
    public function myProposals(Request $request): JsonResponse
    {
        $proposals = Proposal::where('user_id', $request->user()->id)
            ->with(['announcement.category', 'announcement.user'])
            ->latest()
            ->get();

        return response()->json($proposals);
    }

    /**
     * Create a proposal
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'announcement_id' => 'required|exists:announcements,id',
            'price' => 'required|numeric|min:0',
            'delivery_days' => 'required|integer|min:1',
            'description' => 'required|string|min:50',
        ]);

        $announcement = Announcement::findOrFail($validated['announcement_id']);

        // Can't propose to own announcement
        if ($announcement->user_id === $request->user()->id) {
            return response()->json(['message' => 'Nie możesz złożyć oferty do własnego ogłoszenia'], 422);
        }

        // Check if already proposed
        $existing = Proposal::where('announcement_id', $validated['announcement_id'])
            ->where('user_id', $request->user()->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Już złożyłeś ofertę do tego ogłoszenia'], 422);
        }

        $proposal = Proposal::create([
            'announcement_id' => $validated['announcement_id'],
            'user_id' => $request->user()->id,
            'price' => $validated['price'],
            'delivery_days' => $validated['delivery_days'],
            'description' => $validated['description'],
        ]);

        // Update proposals count
        $announcement->increment('proposals_count');

        return response()->json([
            'message' => 'Oferta złożona pomyślnie!',
            'proposal' => $proposal->load('freelancer'),
        ], 201);
    }

    /**
     * Accept a proposal
     */
    public function accept(Request $request, $id): JsonResponse
    {
        $proposal = Proposal::with('announcement')->findOrFail($id);

        // Only announcement owner can accept
        if ($proposal->announcement->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($proposal->status !== 'pending') {
            return response()->json(['message' => 'Oferta już została rozpatrzona'], 422);
        }

        $proposal->accept();

        // Reject other proposals for this announcement
        Proposal::where('announcement_id', $proposal->announcement_id)
            ->where('id', '!=', $id)
            ->where('status', 'pending')
            ->update(['status' => 'rejected', 'rejected_at' => now()]);

        return response()->json([
            'message' => 'Oferta zaakceptowana!',
            'proposal' => $proposal,
        ]);
    }

    /**
     * Reject a proposal
     */
    public function reject(Request $request, $id): JsonResponse
    {
        $proposal = Proposal::with('announcement')->findOrFail($id);

        // Only announcement owner can reject
        if ($proposal->announcement->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($proposal->status !== 'pending') {
            return response()->json(['message' => 'Oferta już została rozpatrzona'], 422);
        }

        $proposal->reject();

        return response()->json([
            'message' => 'Oferta odrzucona',
            'proposal' => $proposal,
        ]);
    }

    /**
     * Withdraw a proposal (as freelancer)
     */
    public function withdraw(Request $request, $id): JsonResponse
    {
        $proposal = Proposal::findOrFail($id);

        // Only proposal owner can withdraw
        if ($proposal->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($proposal->status !== 'pending') {
            return response()->json(['message' => 'Nie możesz wycofać tej oferty'], 422);
        }

        $proposal->update(['status' => 'withdrawn']);

        return response()->json([
            'message' => 'Oferta wycofana',
        ]);
    }
}

