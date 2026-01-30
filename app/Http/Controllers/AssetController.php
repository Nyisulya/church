<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Department;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::with('department')->latest()->paginate(10);
        return view('assets.index', compact('assets'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('assets.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'value' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'condition' => 'required|in:new,good,fair,poor,broken',
            'department_id' => 'nullable|exists:departments,id',
            'description' => 'nullable|string',
        ]);

        Asset::create($validated);

        return redirect()->route('assets.index')->with('success', 'Asset created successfully.');
    }

    public function edit(Asset $asset)
    {
        $departments = Department::all();
        return view('assets.edit', compact('asset', 'departments'));
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'value' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'condition' => 'required|in:new,good,fair,poor,broken',
            'department_id' => 'nullable|exists:departments,id',
            'description' => 'nullable|string',
        ]);

        $asset->update($validated);

        return redirect()->route('assets.index')->with('success', 'Asset updated successfully.');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        return redirect()->route('assets.index')->with('success', 'Asset deleted successfully.');
    }
}
