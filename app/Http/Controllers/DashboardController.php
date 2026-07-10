<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Transaction;
use App\Models\Event;
use App\Models\Attendance;
use App\Models\FollowUp;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user->hasAnyRole(['super_admin', 'admin', 'pastor'])) {
            return redirect()->route('profile.index');
        }

        // Member Growth - Last 6 Months
        $memberGrowth = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = Member::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $memberGrowth->push([
                'month' => $date->format('M Y'),
                'count' => $count
            ]);
        }

        // Financial Overview - Last 6 Months
        $financialData = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $income = Transaction::income()
                ->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('amount');
            
            $expense = Transaction::expense()
                ->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('amount');
            
            $financialData->push([
                'month' => $date->format('M Y'),
                'income' => $income,
                'expense' => $expense
            ]);
        }

        // Attendance Trends - Last 8 weeks
        $attendanceTrends = Event::where('date', '>=', Carbon::now()->subWeeks(8))
            ->where('date', '<=', Carbon::now())
            ->orderBy('date')
            ->get()
            ->map(function($event) {
                return [
                    'name' => $event->name,
                    'date' => $event->date->format('M d'),
                    'count' => $event->attendances()->count()
                ];
            });

        // Demographics - Gender Distribution
        $genderRaw = Member::selectRaw('gender, COUNT(*) as count')
            ->whereNotNull('gender')
            ->where('gender', '!=', '')
            ->groupBy('gender')
            ->get();
        
        $genderLabels = $genderRaw->pluck('gender')->map(fn($g) => ucfirst($g ?? 'Unknown'))->values()->toArray();
        $genderCounts = $genderRaw->pluck('count')->values()->toArray();

        // Demographics - Age Groups
        $members = Member::whereNotNull('date_of_birth')->get();
        
        if ($members->count() > 0) {
            $ageGroupsRaw = $members
                ->map(function($member) {
                    $age = $member->date_of_birth->age;
                    if ($age < 18) return '0-17';
                    if ($age <= 35) return '18-35';
                    if ($age <= 55) return '36-55';
                    return '55+';
                })
                ->groupBy(function($group) {
                    return $group;
                })
                ->map(function($group, $key) {
                    return [
                        'label' => $key,
                        'count' => $group->count()
                    ];
                })
                ->values();
                
            $ageLabels = $ageGroupsRaw->pluck('label')->values()->toArray();
            $ageCounts = $ageGroupsRaw->pluck('count')->values()->toArray();
        } else {
            $ageLabels = ['0-17', '18-35', '36-55', '55+'];
            $ageCounts = [0, 0, 0, 0];
        }

        // Demographics - Marital Status
        $maritalRaw = Member::selectRaw('marital_status, COUNT(*) as count')
            ->whereNotNull('marital_status')
            ->where('marital_status', '!=', '')
            ->groupBy('marital_status')
            ->get();
            
        $maritalLabels = $maritalRaw->pluck('marital_status')->map(fn($m) => ucfirst($m ?? 'Unknown'))->values()->toArray();
        $maritalCounts = $maritalRaw->pluck('count')->values()->toArray();

        return view('dashboard', compact(
            'memberGrowth', 'financialData', 'attendanceTrends', 
            'genderLabels', 'genderCounts', 
            'ageLabels', 'ageCounts', 
            'maritalLabels', 'maritalCounts'
        ));
    }
}
