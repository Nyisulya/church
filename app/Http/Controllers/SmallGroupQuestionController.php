<?php

namespace App\Http\Controllers;

use App\Models\SmallGroupQuestion;
use Illuminate\Http\Request;

class SmallGroupQuestionController extends Controller
{
    /**
     * Display a listing of all questions
     */
    public function index()
    {
        $questions = SmallGroupQuestion::ordered()->get();
        return view('small-groups.questions.index', compact('questions'));
    }

    /**
     * Show the form for creating a new question
     */
    public function create()
    {
        return view('small-groups.questions.create');
    }

    /**
     * Store a newly created question
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'question_sw' => 'required|string',
            'question_en' => 'required|string',
            'response_type' => 'required|in:number,yes_no,text,amount',
            'category' => 'required|in:evangelism,bible_study,community_service,other',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        SmallGroupQuestion::create($validated);

        return redirect()->route('small-groups.questions.index')
            ->with('success', 'Question created successfully.');
    }

    /**
     * Show the form for editing a question
     */
    public function edit(SmallGroupQuestion $question)
    {
        return view('small-groups.questions.edit', compact('question'));
    }

    /**
     * Update the specified question
     */
    public function update(Request $request, SmallGroupQuestion $question)
    {
        $validated = $request->validate([
            'question_sw' => 'required|string',
            'question_en' => 'required|string',
            'response_type' => 'required|in:number,yes_no,text,amount',
            'category' => 'required|in:evangelism,bible_study,community_service,other',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $question->update($validated);

        return redirect()->route('small-groups.questions.index')
            ->with('success', 'Question updated successfully.');
    }

    /**
     * Toggle question active status
     */
    public function toggleStatus(SmallGroupQuestion $question)
    {
        $question->update(['is_active' => !$question->is_active]);

        $status = $question->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Question {$status} successfully.");
    }

    /**
     * Remove the specified question
     */
    public function destroy(SmallGroupQuestion $question)
    {
        $question->delete();

        return redirect()->route('small-groups.questions.index')
            ->with('success', 'Question deleted successfully.');
    }
}
