<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Department;

class ProfileController extends Controller
{
    /**
     * Display the authenticated user's profile
     */
    public function index()
    {
        $user = Auth::user();
        $member = $user->member;

        return view('profile.index', compact('user', 'member'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        $user = Auth::user();
        $member = $user->member;
        $departments = Department::all();

        return view('profile.edit', compact('user', 'member', 'departments'));
    }

    /**
     * Update the user's profile information
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $member = $user->member;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id . ($member ? '|unique:members,email,' . $member->id : ''),
            'phone' => 'nullable|string|max:20',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date|before:today',
            'marital_status' => 'required|in:single,married,widowed,divorced',
            'wedding_date' => 'nullable|date|before_or_equal:today',
            'address' => 'nullable|string|max:500',
            'salvation_date' => 'nullable|date|before_or_equal:today',
            'baptism_date' => 'nullable|date|before_or_equal:today',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'departments' => 'nullable|array',
            'departments.*' => 'exists:departments,id',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update user basic details
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        // Update password if entered
        if ($request->filled('password')) {
            $userData['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->update($userData);

        // Handle profile photo upload
        $photoPath = null;
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($member && $member->profile_photo) {
                Storage::disk('public')->delete($member->profile_photo);
            }

            // Store new photo
            $photoPath = $request->file('profile_photo')->store('profile_photos', 'public');
        }

        // Create or update member profile
        if (!$member) {
            // Create new member profile
            $member = \App\Models\Member::create([
                'user_id' => $user->id,
                'full_name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'gender' => $validated['gender'],
                'date_of_birth' => $validated['date_of_birth'],
                'marital_status' => $validated['marital_status'],
                'wedding_date' => $validated['wedding_date'],
                'address' => $validated['address'],
                'salvation_date' => $validated['salvation_date'],
                'baptism_date' => $validated['baptism_date'],
                'emergency_contact_name' => $validated['emergency_contact_name'],
                'emergency_contact_phone' => $validated['emergency_contact_phone'],
                'profile_photo' => $photoPath,
                'member_number' => 'MEM' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
                'status' => 'active',
            ]);
        } else {
            // Update existing member profile
            $member->update([
                'full_name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'gender' => $validated['gender'],
                'date_of_birth' => $validated['date_of_birth'],
                'marital_status' => $validated['marital_status'],
                'wedding_date' => $validated['wedding_date'],
                'address' => $validated['address'],
                'salvation_date' => $validated['salvation_date'],
                'baptism_date' => $validated['baptism_date'],
                'emergency_contact_name' => $validated['emergency_contact_name'],
                'emergency_contact_phone' => $validated['emergency_contact_phone'],
                'profile_photo' => $photoPath ?? $member->profile_photo,
            ]);
        }

        // Sync departments
        if (isset($validated['departments'])) {
            $member->departments()->sync($validated['departments']);
        } else {
            $member->departments()->detach();
        }

        return redirect()->route('profile.index')
            ->with('success', 'Profile updated successfully!');
    }
    
    /**
     * Download member QR code
     */
    public function downloadQr()
    {
        $user = Auth::user();
        $member = $user->member;
        
        if (!$member) {
            return redirect()->route('profile.index')
                ->with('error', 'No member profile found.');
        }
        
        // Generate QR code as PNG
        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
            ->size(400)
            ->margin(2)
            ->generate($member->member_number);
        
        // Return as download
        return response($qrCode)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="my-qr-code-' . $member->member_number . '.png"');
    }
}
