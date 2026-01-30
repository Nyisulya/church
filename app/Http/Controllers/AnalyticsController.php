<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Transaction;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        // 1. Member Growth (Last 12 Months)
        $memberGrowth = Member::where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->created_at)->format('Y-m');
            })
            ->map(function ($row) {
                return [
                    'count' => $row->count(),
                    'month_year' => Carbon::parse($row->first()->created_at)->format('Y-m'),
                ];
            })
            ->sortBy('month_year')
            ->values();

        // 2. Financial Overview (Income vs Expense - Last 6 Months)
        $financials = Transaction::where('transaction_date', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->transaction_date)->format('Y-m') . '|' . $date->type;
            })
            ->map(function ($row) {
                $first = $row->first();
                return [
                    'total' => $row->sum('amount'),
                    'type' => $first->type,
                    'month_year' => Carbon::parse($first->transaction_date)->format('Y-m'),
                ];
            })
            ->values();

        // 3. Attendance Trends (Last 8 Weeks)
        $attendance = Attendance::where('date', '>=', Carbon::now()->subWeeks(8))
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->date)->format('Y-m-d');
            })
            ->map(function ($row) {
                return [
                    'count' => $row->unique('member_id')->count(),
                    'event_date' => Carbon::parse($row->first()->date)->format('Y-m-d'),
                ];
            })
            ->sortBy('event_date')
            ->values();

        // 4. Demographics
        $genderStats = Member::whereNotNull('gender')
            ->get()
            ->groupBy('gender')
            ->map(function ($row) {
                return [
                    'gender' => $row->first()->gender,
                    'total' => $row->count()
                ];
            })
            ->values();

        $maritalStats = Member::whereNotNull('marital_status')
            ->get()
            ->groupBy('marital_status')
            ->map(function ($row) {
                return [
                    'marital_status' => $row->first()->marital_status,
                    'total' => $row->count()
                ];
            })
            ->values();

        // Age Groups
        $ageGroups = Member::whereNotNull('date_of_birth')
            ->get()
            ->map(function ($member) {
                $age = $member->date_of_birth->age;
                if ($age < 18) return '0-17';
                if ($age <= 35) return '18-35';
                if ($age <= 55) return '36-55';
                return '55+';
            })
            ->groupBy(function ($group) {
                return $group;
            })
            ->map(function ($row, $key) {
                return [
                    'age_group' => $key,
                    'total' => $row->count()
                ];
            })
            ->values();

        return view('reports.analytics', compact(
            'memberGrowth', 
            'financials', 
            'attendance', 
            'genderStats', 
            'maritalStats', 
            'ageGroups'
        ));
    }
}
