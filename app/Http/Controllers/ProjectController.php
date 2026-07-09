<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mark projects as viewed
        if (Auth::check()) {
            Auth::user()->update(['last_viewed_projects_at' => now()]);
        }

        $user = Auth::user();
        
        if ($user->hasAnyRole(['super_admin', 'admin', 'pastor', 'treasurer'])) {
            // Admins see all projects
            $projects = Project::latest()->paginate(10);
        } else {
            // Members see only active projects
            $projects = Project::where('status', 'active')
                ->where(function($q) {
                    $q->whereNull('end_date')
                      ->orWhere('end_date', '>=', now());
                })
                ->latest()
                ->paginate(10);
        }

        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Project::class);
        return view('projects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Project::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'goal_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,completed,on_hold',
        ]);

        $validated['created_by'] = Auth::id();

        Project::create($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $groups = \App\Models\SmallGroup::with('members')->get()->map(function($group) use ($project) {
            $memberIds = $group->members->pluck('id')->toArray();
            
            $totalPledged = \App\Models\Pledge::whereIn('member_id', $memberIds)
                ->where('purpose', $project->name)
                ->sum('amount');
                
            $totalPaid = \App\Models\Pledge::whereIn('member_id', $memberIds)
                ->where('purpose', $project->name)
                ->sum('amount_paid');
                
            $targetGoal = \App\Models\ProjectGroupGoal::where('project_id', $project->id)
                ->where('small_group_id', $group->id)
                ->first();
                
            $targetAmount = $targetGoal ? $targetGoal->target_amount : 0.00;
            
            return [
                'id' => $group->id,
                'name' => $group->name,
                'target_amount' => $targetAmount,
                'total_pledged' => $totalPledged,
                'total_paid' => $totalPaid,
                'percentage' => $targetAmount > 0 ? min(100, round(($totalPaid / $targetAmount) * 100, 1)) : 0,
            ];
        });

        // Get total pledged and total paid across the entire project for comparison
        $totalProjectPledged = \App\Models\Pledge::where('purpose', $project->name)->sum('amount');
        $totalProjectPaid = \App\Models\Pledge::where('purpose', $project->name)->sum('amount_paid');

        return view('projects.show', compact('project', 'groups', 'totalProjectPledged', 'totalProjectPaid'));
    }

    /**
     * Update target goals for each small group for this project.
     */
    public function updateGroupGoals(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'targets' => 'required|array',
            'targets.*' => 'nullable|numeric|min:0',
        ]);

        foreach ($validated['targets'] as $groupId => $targetAmount) {
            \App\Models\ProjectGroupGoal::updateOrCreate(
                ['project_id' => $project->id, 'small_group_id' => $groupId],
                ['target_amount' => $targetAmount ?? 0.00]
            );
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Group goals updated successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $this->authorize('update', $project);
        return view('projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'goal_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,completed,on_hold',
        ]);

        $project->update($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}
