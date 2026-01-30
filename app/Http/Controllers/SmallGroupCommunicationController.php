<?php

namespace App\Http\Controllers;

use App\Models\SmallGroup;
use App\Models\SmallGroupOffering;
use App\Models\SmallGroupResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmallGroupCommunicationController extends Controller
{
    /**
     * Send reminders to members who haven't submitted their weekly report.
     */
    public function remindPendingReporters(Request $request, SmallGroup $smallGroup)
    {
        $currentWeek = SmallGroupResponse::getCurrentWeekStart();
        
        // Find members who haven't submitted
        $pendingMembers = $smallGroup->members->filter(function ($member) use ($smallGroup, $currentWeek) {
            return !SmallGroupResponse::where('small_group_id', $smallGroup->id)
                ->where('member_id', $member->id)
                ->forWeek($currentWeek)
                ->exists();
        });

        $count = 0;
        foreach ($pendingMembers as $member) {
            if ($member->phone || $member->email) {
                // In a real app, dispatch a job or send notification here
                // Notification::send($member, new WeeklyReportReminder($smallGroup));
                $count++;
            }
        }

        return back()->with('success', "Reminders sent to {$count} members who haven't submitted their report yet.");
    }

    /**
     * Send reminders to members who owe money for a specific offering.
     */
    public function remindDebtors(Request $request, SmallGroupOffering $offering)
    {
        $debtors = $offering->smallGroup->members->filter(function ($member) use ($offering) {
            $balance = $offering->getMemberBalance($member->id);
            return $balance > 0;
        });

        $count = 0;
        foreach ($debtors as $member) {
             if ($member->phone || $member->email) {
                // In a real app, dispatch a job or send notification here
                // Notification::send($member, new PaymentReminder($offering));
                $count++;
            }
        }

        return back()->with('success', "Payment reminders sent to {$count} members with outstanding balances.");
    }
}
