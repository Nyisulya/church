<?php

namespace App\Http\Controllers;

use App\Models\CareRequest;
use App\Models\User;
use App\Notifications\NewCareRequestNotification;
use App\Notifications\CareRequestResponseNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CareRequestController extends Controller
{
    /**
     * Display member's own care requests
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user->member) {
            return redirect()->route('profile.index')
                ->with('warning', __('Please complete your member profile first.'));
        }

        $careRequests = CareRequest::forMember($user->member->id)
            ->with('leader')
            ->latest()
            ->paginate(10);

        return view('care-requests.index', compact('careRequests'));
    }

    /**
     * Show form to create a new care request
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!$user->member) {
            return redirect()->route('profile.index')
                ->with('warning', __('Please complete your member profile first.'));
        }

        // Get users with leader roles (pastor, admin, department_leader)
        $leaders = User::role(['super_admin', 'admin', 'pastor', 'department_leader'])
            ->orderBy('name')
            ->get();

        $categories = CareRequest::CATEGORIES;
        $priorities = CareRequest::PRIORITIES;

        return view('care-requests.create', compact('leaders', 'categories', 'priorities'));
    }

    /**
     * Store a new care request
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->member) {
            return redirect()->route('profile.index')
                ->with('warning', __('Please complete your member profile first.'));
        }

        $validated = $request->validate([
            'leader_id' => 'required|exists:users,id',
            'category' => 'required|in:sick,need_visit,need_prayer,counseling,financial_help,other',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:low,normal,high,urgent',
        ]);

        $validated['member_id'] = $user->member->id;
        $validated['status'] = 'pending';

        $careRequest = CareRequest::create($validated);

        // Notify the leader
        $leader = User::find($validated['leader_id']);
        $leader->notify(new NewCareRequestNotification($careRequest));

        return redirect()->route('care-requests.index')
            ->with('success', __('Your care request has been submitted successfully. The leader will be notified.'));
    }

    /**
     * Display a single care request
     */
    public function show(CareRequest $careRequest)
    {
        $user = Auth::user();
        
        // Check if user has permission to view this request
        $canView = false;
        
        // Member can view their own requests
        if ($user->member && $careRequest->member_id === $user->member->id) {
            $canView = true;
        }
        
        // Leader can view requests assigned to them
        if ($careRequest->leader_id === $user->id) {
            $canView = true;
        }
        
        // Super admin can view all
        if ($user->hasRole('super_admin')) {
            $canView = true;
        }

        if (!$canView) {
            abort(403, __('You do not have permission to view this request.'));
        }

        $careRequest->load(['member', 'leader']);

        return view('care-requests.show', compact('careRequest'));
    }

    /**
     * Leader dashboard - view requests assigned to them
     */
    public function leaderDashboard()
    {
        $user = Auth::user();

        // Check if user has leader role
        if (!$user->hasAnyRole(['super_admin', 'admin', 'pastor', 'department_leader'])) {
            abort(403, __('You do not have permission to access this page.'));
        }

        // Super admin sees all, others see only their assigned requests
        if ($user->hasRole('super_admin')) {
            $careRequests = CareRequest::with(['member', 'leader'])
                ->latest()
                ->paginate(15);
        } else {
            $careRequests = CareRequest::forLeader($user->id)
                ->with('member')
                ->latest()
                ->paginate(15);
        }

        // Get counts for stats
        $stats = [
            'pending' => CareRequest::forLeader($user->id)->pending()->count(),
            'in_progress' => CareRequest::forLeader($user->id)->inProgress()->count(),
            'completed' => CareRequest::forLeader($user->id)->completed()->count(),
        ];

        return view('care-requests.leader-dashboard', compact('careRequests', 'stats'));
    }

    /**
     * Leader responds to a care request
     */
    public function respond(Request $request, CareRequest $careRequest)
    {
        $user = Auth::user();

        // Only the assigned leader or super_admin can respond
        if ($careRequest->leader_id !== $user->id && !$user->hasRole('super_admin')) {
            abort(403, __('You do not have permission to respond to this request.'));
        }

        $validated = $request->validate([
            'response' => 'required|string',
            'leader_notes' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $careRequest->update([
            'response' => $validated['response'],
            'leader_notes' => $validated['leader_notes'] ?? $careRequest->leader_notes,
            'status' => $validated['status'],
            'responded_at' => now(),
        ]);

        // Notify the member about the response
        if ($careRequest->member && $careRequest->member->user) {
            $careRequest->member->user->notify(
                new CareRequestResponseNotification($careRequest, 'response')
            );
        }

        return back()->with('success', __('Response sent successfully. The member has been notified.'));
    }

    /**
     * Update status of a care request
     */
    public function updateStatus(Request $request, CareRequest $careRequest)
    {
        $user = Auth::user();

        // Only the assigned leader or super_admin can update
        if ($careRequest->leader_id !== $user->id && !$user->hasRole('super_admin')) {
            abort(403, __('You do not have permission to update this request.'));
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $oldStatus = $careRequest->status;
        $careRequest->update(['status' => $validated['status']]);

        // Notify member if status changed
        if ($oldStatus !== $validated['status'] && $careRequest->member && $careRequest->member->user) {
            $careRequest->member->user->notify(
                new CareRequestResponseNotification($careRequest, 'status_update')
            );
        }

        return back()->with('success', __('Status updated successfully.'));
    }
}
