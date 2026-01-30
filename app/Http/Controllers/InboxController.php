<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InboxController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->whereNotIn('type', [
                \App\Notifications\NewCareRequestNotification::class,
                \App\Notifications\CareRequestResponseNotification::class,
                \App\Notifications\BirthdayGreetingNotification::class,
                \App\Notifications\AnniversaryGreetingNotification::class,
            ])
            ->paginate(10);
        return view('inbox.index', compact('notifications'));
    }

    public function show($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return view('inbox.show', compact('notification'));
    }
}
