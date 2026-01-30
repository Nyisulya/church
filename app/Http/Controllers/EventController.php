<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Display a listing of events.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Event::class);
        
        $query = Event::query()->withCount('attendances');
        
        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Filter by status (upcoming/past)
        if ($request->filled('status')) {
            if ($request->status === 'upcoming') {
                $query->where('date', '>=', Carbon::today());
            } elseif ($request->status === 'past') {
                $query->where('date', '<', Carbon::today());
            }
        }
        
        $events = $query->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(15);
        
        // Statistics for dashboard cards
        $stats = [
            'total' => Event::count(),
            'upcoming' => Event::where('date', '>=', Carbon::today())->count(),
            'this_month' => Event::whereMonth('date', Carbon::now()->month)->count(),
        ];
        
        return view('events.index', compact('events', 'stats'));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create(): View
    {
        $this->authorize('create', Event::class);
        return view('events.create');
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Event::class);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:service,meeting,event',
            'date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'agenda' => 'nullable|string',
            'is_recurring' => 'nullable|boolean',
            'recurrence_pattern' => 'nullable|in:weekly,biweekly,monthly',
        ]);
        
        $event = Event::create($validated);
        
        return redirect()->route('events.show', $event)
            ->with('status', 'Event created successfully');
    }

    /**
     * Display the event calendar.
     */
    public function calendar(): View
    {
        $events = Event::all()->map(function ($event) {
            return [
                'title' => $event->name,
                'start' => $event->date->format('Y-m-d') . ($event->start_time ? 'T' . $event->start_time->format('H:i:s') : ''),
                'end' => $event->date->format('Y-m-d') . ($event->end_time ? 'T' . $event->end_time->format('H:i:s') : ''),
                'url' => route('events.show', $event),
                'color' => match($event->type) {
                    'service' => '#3788d8',
                    'meeting' => '#28a745',
                    'event' => '#6c757d',
                    default => '#3788d8',
                }
            ];
        });

        return view('events.calendar', compact('events'));
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event): View
    {
        $this->authorize('view', $event);
        
        $event->load('attendances.member', 'actionItems.assignedTo');
        
        $stats = [
            'total_attendees' => $event->attendances->count(),
            'present' => $event->attendances->where('status', 'present')->count(),
            'late' => $event->attendances->where('status', 'late')->count(),
            'registered' => $event->attendances->where('status', 'registered')->count(),
        ];

        $user = auth()->user();
        $isRegistered = false;
        if ($user->member) {
            $isRegistered = $event->attendances()->where('member_id', $user->member->id)->exists();
        }
        
        return view('events.show', compact('event', 'stats', 'isRegistered'));
    }

    /**
     * Register the current user (member) for the event.
     */
    public function register(Event $event): RedirectResponse
    {
        $user = auth()->user();

        if (!$user->member) {
            return back()->with('error', 'You must have a member profile to register for events.');
        }

        // Check if already registered
        if ($event->attendances()->where('member_id', $user->member->id)->exists()) {
            return back()->with('info', 'You are already registered for this event.');
        }

        Attendance::create([
            'event_id' => $event->id,
            'member_id' => $user->member->id,
            'status' => 'registered',
            'scanned_at' => null,
            'scanned_by' => null,
        ]);

        return back()->with('success', 'You have successfully registered for this event.');
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Event $event): View
    {
        $this->authorize('update', $event);
        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('update', $event);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:service,meeting,event',
            'date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'agenda' => 'nullable|string',
            'minutes' => 'nullable|string',
            'is_recurring' => 'nullable|boolean',
            'recurrence_pattern' => 'nullable|in:weekly,biweekly,monthly',
        ]);
        
        $event->update($validated);
        
        return redirect()->route('events.show', $event)
            ->with('status', 'Event updated successfully');
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Event $event): RedirectResponse
    {
        $this->authorize('delete', $event);
        $event->delete();
        
        return redirect()->route('events.index')
            ->with('status', 'Event deleted successfully');
    }
}
