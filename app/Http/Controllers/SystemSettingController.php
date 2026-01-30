<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SystemSettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::all()->pluck('value', 'key');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token', 'church_logo');

        // Handle text fields
        foreach ($data as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Handle Logo Upload
        if ($request->hasFile('church_logo')) {
            $path = $request->file('church_logo')->store('public/settings');
            // Remove 'public/' from path for storage link access
            $publicPath = str_replace('public/', '', $path);
            
            SystemSetting::updateOrCreate(
                ['key' => 'church_logo'],
                ['value' => $publicPath]
            );
        }

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}
