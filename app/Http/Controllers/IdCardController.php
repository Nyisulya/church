<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IdCardController extends Controller
{
    public function show(Member $member)
    {
        // Authorization check
        $user = Auth::user();
        if (!$user->hasAnyRole(['super_admin', 'admin', 'pastor'])) {
            // Regular members can only view their own ID card
            if (!$user->member || $user->member->id !== $member->id) {
                abort(403);
            }
        }

        return view('members.id-card', compact('member'));
    }

    public function download(Member $member)
    {
         // Authorization check
         $user = Auth::user();
         if (!$user->hasAnyRole(['super_admin', 'admin', 'pastor'])) {
             // Regular members can only view their own ID card
             if (!$user->member || $user->member->id !== $member->id) {
                 abort(403);
             }
         }

        return view('members.id-card-download', compact('member'));
    }
}
