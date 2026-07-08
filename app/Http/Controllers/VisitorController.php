<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use App\Models\Member;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Visitor::query()->with('assignedTo');

        if ($request->filled('status')) {
            $query->where('follow_up_status', $request->status);
        }

        $visitors = $query->latest('visit_date')->paginate(15);
        
        $stats = [
            'total' => Visitor::count(),
            'pending' => Visitor::where('follow_up_status', 'pending')->count(),
            'converted' => Visitor::where('follow_up_status', 'member')->count(),
        ];

        return view('visitors.index', compact('visitors', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $members = Member::orderBy('full_name')->get();
        return view('visitors.create', compact('members'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'visit_date' => 'required|date',
            'how_found_us' => 'nullable|string|max:255',
            'assigned_to_member_id' => 'nullable|exists:members,id',
            'notes' => 'nullable|string',
        ]);

        $visitor = Visitor::create($validated);

        return redirect()->route('visitors.show', $visitor)
            ->with('success', 'Visitor recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Visitor $visitor)
    {
        $visitor->load('assignedTo');
        $members = Member::orderBy('full_name')->get();
        return view('visitors.show', compact('visitor', 'members'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Visitor $visitor)
    {
        $members = Member::orderBy('full_name')->get();
        return view('visitors.edit', compact('visitor', 'members'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Visitor $visitor)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'visit_date' => 'required|date',
            'how_found_us' => 'nullable|string|max:255',
            'assigned_to_member_id' => 'nullable|exists:members,id',
            'follow_up_status' => 'required|in:pending,contacted,member,dropped',
            'notes' => 'nullable|string',
        ]);

        $visitor->update($validated);

        return redirect()->route('visitors.show', $visitor)
            ->with('success', 'Visitor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Visitor $visitor)
    {
        $visitor->delete();
        return redirect()->route('visitors.index')
            ->with('success', 'Visitor deleted successfully.');
    }
}
