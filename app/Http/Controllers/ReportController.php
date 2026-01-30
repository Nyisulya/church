<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Transaction;
use App\Models\Event;
use App\Models\Attendance;
use App\Models\Visit;
use App\Models\FollowUp;
use App\Models\PrayerRequest;
use App\Models\Pledge;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function dashboard(): View
    {
        $stats = [
            'total_members' => Member::count(),
            'active_members' => Member::where('status', 'active')->count(),
            'total_income' => Transaction::income()->sum('amount'),
            'total_expenses' => Transaction::expense()->sum('amount'),
            'total_events' => Event::count(),
            'avg_attendance' => Attendance::count() > 0 ? round(Attendance::count() / Event::count()) : 0,
            'pending_followups' => FollowUp::pending()->count(),
            'active_prayers' => PrayerRequest::active()->count(),
        ];

        return view('reports.dashboard', compact('stats'));
    }

    public function memberReports(Request $request): View
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfYear()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        // Member growth by month
        $memberGrowth = Member::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('strftime("%Y-%m", created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Demographics
        $genderStats = Member::selectRaw('gender, COUNT(*) as count')
            ->groupBy('gender')
            ->get();

        $maritalStats = Member::selectRaw('marital_status, COUNT(*) as count')
            ->groupBy('marital_status')
            ->get();

        // Department distribution
        $departmentStats = DB::table('department_member')
            ->join('departments', 'departments.id', '=', 'department_member.department_id')
            ->selectRaw('departments.name, COUNT(*) as count')
            ->groupBy('departments.name')
            ->get();

        return view('reports.members', compact(
            'memberGrowth',
            'genderStats',
            'maritalStats',
            'departmentStats',
            'startDate',
            'endDate'
        ));
    }

    public function financialReports(Request $request): View
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $totalIncome = Transaction::income()->byDateRange($startDate, $endDate)->sum('amount');
        $totalExpense = Transaction::expense()->byDateRange($startDate, $endDate)->sum('amount');
        $netBalance = $totalIncome - $totalExpense;

        // Income by category
        $incomeByCategory = Transaction::income()
            ->byDateRange($startDate, $endDate)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        // Expense by category
        $expenseByCategory = Transaction::expense()
            ->byDateRange($startDate, $endDate)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        // Monthly trends
        $monthlyTrends = Transaction::byDateRange($startDate, $endDate)
            ->selectRaw('strftime("%Y-%m", transaction_date) as month, type, SUM(amount) as total')
            ->groupBy('month', 'type')
            ->orderBy('month')
            ->get();

        // Pledge statistics
        $totalPledges = Pledge::sum('amount');
        $totalPaid = Pledge::sum('amount_paid');
        $pledgeCompletion = $totalPledges > 0 ? round(($totalPaid / $totalPledges) * 100, 2) : 0;

        return view('reports.financial', compact(
            'totalIncome',
            'totalExpense',
            'netBalance',
            'incomeByCategory',
            'expenseByCategory',
            'monthlyTrends',
            'totalPledges',
            'totalPaid',
            'pledgeCompletion',
            'startDate',
            'endDate'
        ));
    }

    public function attendanceReports(Request $request): View
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        // Attendance by event
        $attendanceByEvent = Event::with('attendances')
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->map(function ($event) {
                return [
                    'event' => $event->name,
                    'date' => $event->date->format('M d, Y'),
                    'count' => $event->attendances->count()
                ];
            });

        // Average attendance
        $avgAttendance = $attendanceByEvent->avg('count') ?? 0;

        return view('reports.attendance', compact(
            'attendanceByEvent',
            'avgAttendance',
            'startDate',
            'endDate'
        ));
    }

    public function pastoralCareReports(Request $request): View
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        // Visit statistics
        $totalVisits = Visit::whereBetween('visit_date', [$startDate, $endDate])->count();
        $visitsByType = Visit::whereBetween('visit_date', [$startDate, $endDate])
            ->selectRaw('visit_type, COUNT(*) as count')
            ->groupBy('visit_type')
            ->get();

        // Follow-up statistics
        $totalFollowUps = FollowUp::whereBetween('created_at', [$startDate, $endDate])->count();
        $completedFollowUps = FollowUp::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        $completionRate = $totalFollowUps > 0 ? round(($completedFollowUps / $totalFollowUps) * 100, 2) : 0;

        // Prayer statistics
        $totalPrayers = PrayerRequest::whereBetween('request_date', [$startDate, $endDate])->count();
        $answeredPrayers = PrayerRequest::where('status', 'answered')
            ->whereBetween('request_date', [$startDate, $endDate])
            ->count();

        return view('reports.pastoral-care', compact(
            'totalVisits',
            'visitsByType',
            'totalFollowUps',
            'completedFollowUps',
            'completionRate',
            'totalPrayers',
            'answeredPrayers',
            'startDate',
            'endDate'
        ));
    }
}
