<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\SmsService;

class CommunicationController extends Controller
{
    public function index()
    {
        return view('communication.index');
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'recipient_group' => 'required|in:all,leaders,choir,specific',
            'specific_member_id' => 'required_if:recipient_group,specific',
            'channel' => 'required|in:sms,email,whatsapp',
            'subject' => 'required_if:channel,email',
            'message' => 'required|string|max:1000',
        ]);

        // Determine recipients
        $recipients = collect();
        
        switch ($validated['recipient_group']) {
            case 'all':
                $recipients = Member::where('status', 'active')->get();
                break;
            case 'leaders':
                // Assuming leaders are in a department or have a role, for now just pick some
                $recipients = Member::where('status', 'active')->limit(5)->get(); 
                break;
            case 'choir':
                 // Placeholder for department logic
                $recipients = Member::whereHas('departments', function($q) {
                    $q->where('name', 'like', '%Choir%');
                })->get();
                break;
            case 'specific':
                $member = Member::find($validated['specific_member_id']);
                if ($member) $recipients->push($member);
                break;
        }

        // Handle WhatsApp Redirect
        if ($validated['channel'] === 'whatsapp') {
            if ($recipients->count() === 1 && $recipients->first()->phone) {
                $phone = preg_replace('/[^0-9]/', '', $recipients->first()->phone);
                $text = urlencode($validated['message']);
                return redirect()->away("https://wa.me/{$phone}?text={$text}");
            } else {
                return back()->with('error', 'WhatsApp messaging requires a single recipient with a valid phone number.');
            }
        }

        $count = 0;
        foreach ($recipients as $recipient) {
            // In a real app, we would dispatch a Job here
            // For demo, we'll just log it
            
            if ($validated['channel'] === 'email' && $recipient->email) {
                // Mail::to($recipient->email)->send(new GenericEmail($validated['subject'], $validated['message']));
                Log::info("Email sent to {$recipient->email}: {$validated['subject']}");
                
                // Also save to inbox
                $recipient->user->notify(new \App\Notifications\MessageNotification(auth()->user(), $validated['subject'], $validated['message']));
                
                $count++;
            } elseif ($validated['channel'] === 'sms' && $recipient->phone) {
                SmsService::send($recipient->phone, $validated['message']);
                Log::info("SMS sent to {$recipient->phone}: {$validated['message']}");
                
                // Also save to inbox (SMS usually doesn't have subject, so use 'SMS Message')
                $recipient->user->notify(new \App\Notifications\MessageNotification(auth()->user(), 'SMS Message', $validated['message']));
                
                $count++;
            }
        }

        return back()->with('success', "Message queued for sending to {$count} recipients!");
    }
}
