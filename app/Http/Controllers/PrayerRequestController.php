<?php

namespace App\Http\Controllers;

use App\Models\PrayerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrayerRequestController extends Controller
{
    // Public prayer wall
    public function wall()
    {
        $prayers = PrayerRequest::where('is_private', false)
            ->where('status', 'active')
            ->with('member')
            ->latest()
            ->paginate(10);
            
        return view('prayer-wall.index', compact('prayers'));
    }

    // My prayer requests
    public function myRequests()
    {
        $user = Auth::user();
        if (!$user->member) {
            return redirect()->route('profile.index')->with('warning', 'Please create a member profile first.');
        }

        $prayers = PrayerRequest::where('member_id', $user->member->id)
            ->latest()
            ->get();
            
        return view('prayer-wall.my-requests', compact('prayers'));
    }

    // Store new prayer request
    public function store(Request $request)
    {
        $validated = $request->validate([
            'request' => 'required|string',
            'is_private' => 'boolean',
        ]);

        $user = Auth::user();
        if (!$user->member) {
            return back()->with('error', 'Please create a member profile first.');
        }

        PrayerRequest::create([
            'member_id' => $user->member->id,
            'request' => $validated['request'],
            'request_date' => now(),
            'is_private' => $request->has('is_private'),
            'created_by' => $user->id,
            'status' => 'active',
        ]);

        return back()->with('success', 'Prayer request submitted successfully.');
    }

    // Increment prayer count (AJAX)
    public function incrementPrayer(PrayerRequest $prayer)
    {
        $prayer->increment('prayer_count');
        return response()->json(['count' => $prayer->prayer_count]);
    }

    // Mark as answered
    public function markAnswered(Request $request, PrayerRequest $prayer)
    {
        $validated = $request->validate([
            'answer' => 'required|string',
        ]);

        $prayer->markAnswered($validated['answer']);

        return back()->with('success', 'Prayer marked as answered!');
    }
}
