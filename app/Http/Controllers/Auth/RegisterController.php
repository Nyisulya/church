<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign default role
        $user->assignRole('member');

        // Auto-create Member profile
        $member = \App\Models\Member::create([
            'user_id' => $user->id,
            'full_name' => $request->name,
            'email' => $request->email,
            'status' => 'active',
        ]);

        Auth::login($user);

        // Redirect to profile edit page with welcome message
        return redirect()->route('members.edit', $member->id)
            ->with('status', 'Welcome! Please complete your profile information.');
    }
}
