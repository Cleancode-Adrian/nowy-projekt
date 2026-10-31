<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * Get ratings for a user
     */
    public function index($userId): JsonResponse
    {
        // Tylko opinie zaakceptowane przez moderację (tak jak na stronie profilu).
        $ratings = Rating::where('rated_id', $userId)
            ->where('is_approved', true)
            ->with(['rater', 'announcement'])
            ->latest()
            ->paginate(10);

        return response()->json($ratings);
    }

    /**
     * Create a rating
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'announcement_id' => 'required|exists:announcements,id',
            'rated_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $announcement = Announcement::findOrFail($validated['announcement_id']);
        $raterId = (int) $request->user()->id;
        $ratedId = (int) $validated['rated_id'];

        // Can't rate yourself (rated_id z requestu jest stringiem - porównujemy jako int)
        if ($ratedId === $raterId) {
            return response()->json(['message' => 'Nie możesz ocenić samego siebie'], 422);
        }

        if (!$this->isCounterparty($announcement, $raterId, $ratedId)) {
            return response()->json(['message' => 'Możesz ocenić tylko osoby z którymi współpracowałeś'], 422);
        }

        // Check if already rated
        $existing = Rating::where('announcement_id', $validated['announcement_id'])
            ->where('rater_id', $request->user()->id)
            ->where('rated_id', $validated['rated_id'])
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Już oceniłeś tę osobę w tym projekcie'], 422);
        }

        $rating = Rating::create([
            'announcement_id' => $validated['announcement_id'],
            'rater_id' => $request->user()->id,
            'rated_id' => $validated['rated_id'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'is_approved' => false, // wymaga akceptacji admina
        ]);

        return response()->json([
            'message' => 'Ocena dodana!',
            'rating' => $rating->load('rater'),
        ], 201);
    }

    /**
     * Check if can rate
     */
    public function canRate(Request $request, $announcementId, $userId): JsonResponse
    {
        $announcement = Announcement::findOrFail($announcementId);

        $canRate = (int) $userId !== (int) $request->user()->id
            && $this->isCounterparty($announcement, (int) $request->user()->id, (int) $userId);

        $alreadyRated = Rating::where('announcement_id', $announcementId)
            ->where('rater_id', $request->user()->id)
            ->where('rated_id', $userId)
            ->exists();

        return response()->json([
            'can_rate' => $canRate && !$alreadyRated,
            'already_rated' => $alreadyRated,
        ]);
    }

    /**
     * Ocenić można tylko drugą stronę współpracy: właściciel ogłoszenia ocenia
     * wykonawcę z zaakceptowaną ofertą, a ten wykonawca - właściciela.
     */
    private function isCounterparty(Announcement $announcement, int $raterId, int $ratedId): bool
    {
        $accepted = fn (int $freelancerId) => $announcement->proposals()
            ->where('user_id', $freelancerId)
            ->where('status', 'accepted')
            ->exists();

        if ((int) $announcement->user_id === $raterId) {
            return $accepted($ratedId);
        }

        return (int) $announcement->user_id === $ratedId && $accepted($raterId);
    }
}

