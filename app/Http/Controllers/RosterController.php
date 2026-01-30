<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Member;
use App\Models\Roster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RosterController extends Controller
{
    // Admin: View all rosters
    public function index()
    {
        $events = Event::where('date', '>=', now())
            ->orderBy('date')
            ->with(['rosters.member'])
            ->paginate(10);
            
        return view('rosters.index', compact('events'));
    }

    // Admin: Assign a volunteer
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'member_id' => 'required|exists:members,id',
            'role' => 'required|string|max:255',
        ]);

        // Check if already assigned
        $exists = Roster::where('event_id', $validated['event_id'])
            ->where('member_id', $validated['member_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Member is already assigned to this event.');
        }

        Roster::create($validated);

        return back()->with('success', 'Volunteer assigned successfully.');
    }

    // Admin: Remove assignment
    public function destroy(Roster $roster)
    {
        $roster->delete();
        return back()->with('success', 'Assignment removed.');
    }

    // Member: View my roster
    public function myRoster()
    {
        $user = Auth::user();
        if (!$user->member) {
            return redirect()->route('profile.index')->with('warning', 'Please create a member profile first.');
        }

        // Mark roster as viewed
        $user->update(['last_viewed_roster_at' => now()]);

        $rosters = Roster::where('member_id', $user->member->id)
            ->whereHas('event', function ($query) {
                $query->where('date', '>=', now()->subDays(1));
            })
            ->with('event')
            ->get()
            ->sortBy('event.date');

        return view('rosters.my-roster', compact('rosters'));
    }
}
