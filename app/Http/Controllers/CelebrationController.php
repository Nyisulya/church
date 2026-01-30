<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class CelebrationController extends Controller
{
    /**
     * Display upcoming birthdays
     */
    public function birthdays(Request $request): View
    {
        $days = $request->get('days', 30);
        
        // Get all members with birthdays and filter in PHP for database compatibility
        $allMembers = Member::upcomingBirthdays($days)->get();
        
        $upcomingBirthdays = $allMembers->filter(function ($member) use ($days) {
            if (!$member->date_of_birth) return false;
            
            $birthday = $member->date_of_birth->setYear(now()->year);
            if ($birthday->isPast()) {
                $birthday->addYear();
            }
            
            $daysUntil = now()->diffInDays($birthday, false);
            return $daysUntil >= 0 && $daysUntil <= $days;
        })->sortBy(function ($member) {
            $birthday = $member->date_of_birth->setYear(now()->year);
            if ($birthday->isPast()) {
                $birthday->addYear();
            }
            return $birthday;
        });
        
        $todaysBirthdays = Member::todaysBirthdays()->get();
        
        return view('celebrations.birthdays', compact('upcomingBirthdays', 'todaysBirthdays', 'days'));
    }

    /**
     * Display upcoming anniversaries
     */
    public function anniversaries(Request $request): View
    {
        $days = $request->get('days', 30);
        
        $upcomingAnniversaries = Member::whereNotNull('wedding_date')
            ->where('marital_status', 'married')
            ->get()
            ->filter(function ($member) use ($days) {
                if (!$member->wedding_date) return false;
                
                $anniversary = $member->wedding_date->setYear(now()->year);
                if ($anniversary->isPast()) {
                    $anniversary->addYear();
                }
                
                return $anniversary->diffInDays(now(), false) >= 0 && 
                       $anniversary->diffInDays(now(), false) <= $days;
            })
            ->sortBy(function ($member) {
                $anniversary = $member->wedding_date->setYear(now()->year);
                if ($anniversary->isPast()) {
                    $anniversary->addYear();
                }
                return $anniversary;
            });
        
        return view('celebrations.anniversaries', compact('upcomingAnniversaries', 'days'));
    }

    /**
     * Combined celebrations dashboard
     */
    public function dashboard(): View
    {
        $todaysBirthdays = Member::todaysBirthdays()->get();
        
        // Get upcoming birthdays and filter in PHP
        $allMembers = Member::whereNotNull('date_of_birth')->get();
        $upcomingBirthdays = $allMembers->filter(function ($member) {
            if (!$member->date_of_birth) return false;
            
            $birthday = $member->date_of_birth->setYear(now()->year);
            if ($birthday->isPast()) {
                $birthday->addYear();
            }
            
            $daysUntil = now()->diffInDays($birthday, false);
            return $daysUntil >= 0 && $daysUntil <= 7;
        })->sortBy(function ($member) {
            $birthday = $member->date_of_birth->setYear(now()->year);
            if ($birthday->isPast()) {
                $birthday->addYear();
            }
            return $birthday;
        })->take(10);
        
        $upcomingAnniversaries = Member::whereNotNull('wedding_date')
            ->where('marital_status', 'married')
            ->limit(10)
            ->get()
            ->filter(function ($member) {
                if (!$member->wedding_date) return false;
                
                $anniversary = $member->wedding_date->setYear(now()->year);
                if ($anniversary->isPast()) {
                    $anniversary->addYear();
                }
                
                return $anniversary->diffInDays(now(), false) >= 0 && 
                       $anniversary->diffInDays(now(), false) <= 7;
            });
        
        return view('celebrations.dashboard', compact(
            'todaysBirthdays',
            'upcomingBirthdays',
            'upcomingAnniversaries'
        ));
    }
}
