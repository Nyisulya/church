<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class AuditLogController extends Controller
{
    public function index()
    {
        // Only super_admin is allowed to view audit logs
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Unauthorized action. Only Super Administrators can view audit logs.');
        }

        $logs = Activity::with(['causer', 'subject'])
            ->latest()
            ->paginate(25);

        return view('audit-logs.index', compact('logs'));
    }
}
