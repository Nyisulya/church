<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChurchAnnouncementController extends Controller
{
    // List all general announcements
    public function index()
    {
        // Mark announcements as viewed
        if (Auth::check()) {
            Auth::user()->update(['last_viewed_announcements_at' => now()]);
        }

        $announcements = Announcement::general()
            ->with('author')
            ->orderBy('announcement_date', 'desc')
            ->orderBy('priority', 'desc')
            ->paginate(20);
        
        return view('announcements.index', compact('announcements'));
    }

    // Show create form
    public function create()
    {
        return view('announcements.create');
    }

    // Store new announcement
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'announcement_date' => 'required|date',
            'priority' => 'nullable|integer|min:0|max:10',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_general'] = true; // Always general for church announcements
        $validated['department_id'] = null;
        $validated['is_active'] = $request->has('is_active');
        $validated['priority'] = $request->input('priority', 0);

        Announcement::create($validated);

        return redirect()->route('announcements.index')
            ->with('success', 'Matangazo created successfully!');
    }

    // Show single announcement
    public function show(Announcement $announcement)
    {
        return view('announcements.show', compact('announcement'));
    }

    // Show edit form
    public function edit(Announcement $announcement)
    {
        return view('announcements.edit', compact('announcement'));
    }

    // Update announcement
    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'announcement_date' => 'required|date',
            'priority' => 'nullable|integer|min:0|max:10',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['priority'] = $request->input('priority', 0);

        $announcement->update($validated);

        return redirect()->route('announcements.index')
            ->with('success', 'Matangazo updated successfully!');
    }

    // Delete announcement
    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()->route('announcements.index')
            ->with('success', 'Matangazo deleted successfully!');
    }

    // Display current week's announcements (for projection/display)
    public function current()
    {
        $announcements = Announcement::general()
            ->active()
            ->current()
            ->get();

        return view('announcements.display', compact('announcements'));
    }

    // Display announcements for members (within admin layout)
    public function memberView()
    {
        // Mark announcements as viewed
        if (Auth::check()) {
            Auth::user()->update(['last_viewed_announcements_at' => now()]);
        }

        $announcements = Announcement::general()
            ->active()
            ->with('author')
            ->orderBy('announcement_date', 'desc')
            ->orderBy('priority', 'desc')
            ->get();

        return view('announcements.member-view', compact('announcements'));
    }
}
