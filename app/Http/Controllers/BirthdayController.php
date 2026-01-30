<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BirthdayController extends Controller
{
    /**
     * Display the birthday calendar
     */
    public function index(Request $request)
    {
        // Mark birthdays as viewed
        if (auth()->check()) {
            auth()->user()->update(['last_viewed_birthdays_at' => now()]);
        }

        $month = $request->get('month', now()->month);
        $year = now()->year;

        // Get all members with birthdays this month
        $query = Member::whereNotNull('date_of_birth')
            ->whereMonth('date_of_birth', $month);

        if (config('database.default') === 'sqlite') {
            $query->orderByRaw("strftime('%d', date_of_birth)");
        } else {
            $query->orderByRaw("DAY(date_of_birth)");
        }

        $birthdays = $query->get()
            ->map(function ($member) use ($year) {
                $birthday = Carbon::parse($member->date_of_birth);
                return [
                    'member' => $member,
                    'date' => $birthday->day,
                    'age' => $birthday->age,
                    'is_today' => $birthday->isBirthday(),
                    'day_of_week' => $birthday->setYear($year)->format('l'),
                ];
            });

        return view('birthdays.index', compact('birthdays', 'month'));
    }

    /**
     * Display the anniversaries calendar
     */
    public function anniversaries(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = now()->year;

        // Get upcoming anniversaries this month
        $query = Member::where(function($q) use ($month) {
            $q->whereMonth('marriage_date', $month)
              ->orWhereMonth('wedding_date', $month);
        });
            
        // Handle sorting based on database driver
        if (config('database.default') === 'sqlite') {
            $query->orderByRaw("COALESCE(strftime('%d', marriage_date), strftime('%d', wedding_date))");
        } else {
            // MySQL/MariaDB
            $query->orderByRaw("DAY(COALESCE(marriage_date, wedding_date))");
        }

        $anniversaries = $query->get()
            ->map(function ($member) use ($year) {
                $anniversary = Carbon::parse($member->anniversary_date);
                return [
                    'member' => $member,
                    'date' => $anniversary->day,
                    'years' => $anniversary->diffInYears(now()),
                    'is_today' => $member->is_anniversary_today,
                    'day_of_week' => $anniversary->setYear($year)->format('l'),
                ];
            });

        return view('birthdays.anniversaries', compact('anniversaries', 'month'));
    }

    /**
     * Send birthday greeting to a member
     */
    public function sendGreeting(Request $request, Member $member)
    {
        if (!$member->user) {
            return back()->with('error', 'This member does not have a user account.');
        }

        // Send notification
        $senderName = auth()->user()->name;
        if (auth()->user()->member) {
            $senderName = auth()->user()->member->full_name;
        }

        if ($request->input('type') === 'anniversary') {
            $member->user->notify(new \App\Notifications\AnniversaryGreetingNotification($senderName));
            return back()->with('success', "Anniversary greeting sent to {$member->full_name}!");
        } else {
            $member->user->notify(new \App\Notifications\BirthdayGreetingNotification($senderName));
            return back()->with('success', "Birthday greeting sent to {$member->full_name}!");
        }
    }
}
