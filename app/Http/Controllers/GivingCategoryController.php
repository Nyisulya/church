<?php

namespace App\Http\Controllers;

use App\Models\GivingCategory;
use Illuminate\Http\Request;

class GivingCategoryController extends Controller
{
    public function index()
    {
        $categories = GivingCategory::orderBy('order')->get();
        return view('giving-categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:giving_categories',
            'type' => 'required|in:income,expense',
            'description' => 'nullable|string',
        ]);

        $maxOrder = GivingCategory::max('order') ?? 0;
        
        GivingCategory::create([
            ...$validated,
            'order' => $maxOrder + 1,
        ]);

        return back()->with('success', 'Category added successfully.');
    }

    public function update(Request $request, GivingCategory $givingCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:giving_categories,name,' . $givingCategory->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $givingCategory->update($validated);

        return back()->with('success', 'Category updated successfully.');
    }

    public function destroy(GivingCategory $givingCategory)
    {
        $givingCategory->delete();
        return back()->with('success', 'Category deleted successfully.');
    }

    public function toggleStatus(GivingCategory $givingCategory)
    {
        $givingCategory->update(['is_active' => !$givingCategory->is_active]);
        return back()->with('success', 'Category status updated.');
    }
}
