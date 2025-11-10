<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function index()
    {
        $pending = Rating::with(['rater', 'rated', 'announcement'])
            ->where('is_approved', false)
            ->latest()
            ->paginate(20);

        $approved = Rating::with(['rater', 'rated', 'announcement'])
            ->where('is_approved', true)
            ->latest()
            ->paginate(20);

        return view('admin.ratings.index', compact('pending', 'approved'));
    }

    public function approve($id)
    {
        $rating = Rating::findOrFail($id);

        $rating->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Opinia została zaakceptowana!');
    }

    public function reject($id)
    {
        $rating = Rating::findOrFail($id);
        $rating->delete();

        return redirect()->back()->with('success', 'Opinia została odrzucona i usunięta!');
    }
}

