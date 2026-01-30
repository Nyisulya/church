@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">❓ Weekly Report Questions</h1>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif

    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('small-groups.questions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New Question
            </a>
            <a href="{{ route('small-groups.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Small Groups
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Questions ({{ $questions->count() }})</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Question (Swahili)</th>
                                <th>Question (English)</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($questions as $question)
                            <tr>
                                <td>{{ $question->order }}</td>
                                <td>{{ Str::limit($question->question_sw, 50) }}</td>
                                <td>{{ Str::limit($question->question_en, 50) }}</td>
                                <td><span class="badge badge-secondary">{{ $question->getResponseTypeLabel() }}</span></td>
                                <td><span class="badge {{ $question->getCategoryBadgeClass() }}">{{ $question->getCategoryLabel() }}</span></td>
                                <td>
                                    @if($question->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('small-groups.questions.edit', $question) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('small-groups.questions.toggle', $question) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-{{ $question->is_active ? 'warning' : 'success' }}">
                                            <i class="fas fa-toggle-{{ $question->is_active ? 'off' : 'on' }}"></i>
                                            {{ $question->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('small-groups.questions.destroy', $question) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this question?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No questions found. Create your first question!</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
