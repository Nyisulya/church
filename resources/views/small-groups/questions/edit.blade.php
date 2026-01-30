@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">✏️ Edit Question</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <form action="{{ route('small-groups.questions.update', $question) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="question_sw">Question in Swahili <span class="text-danger">*</span></label>
                            <textarea name="question_sw" id="question_sw" class="form-control @error('question_sw') is-invalid @enderror" rows="3" required>{{ old('question_sw', $question->question_sw) }}</textarea>
                            @error('question_sw')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="question_en">Question in English <span class="text-danger">*</span></label>
                            <textarea name="question_en" id="question_en" class="form-control @error('question_en') is-invalid @enderror" rows="3" required>{{ old('question_en', $question->question_en) }}</textarea>
                            @error('question_en')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="response_type">Response Type <span class="text-danger">*</span></label>
                            <select name="response_type" id="response_type" class="form-control @error('response_type') is-invalid @enderror" required>
                                <option value="number" {{ old('response_type', $question->response_type) === 'number' ? 'selected' : '' }}>Number</option>
                                <option value="yes_no" {{ old('response_type', $question->response_type) === 'yes_no' ? 'selected' : '' }}>Yes/No</option>
                                <option value="text" {{ old('response_type', $question->response_type) === 'text' ? 'selected' : '' }}>Text</option>
                                <option value="amount" {{ old('response_type', $question->response_type) === 'amount' ? 'selected' : '' }}>Amount (TSh)</option>
                            </select>
                            @error('response_type')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="category">Category <span class="text-danger">*</span></label>
                            <select name="category" id="category" class="form-control @error('category') is-invalid @enderror" required>
                                <option value="evangelism" {{ old('category', $question->category) === 'evangelism' ? 'selected' : '' }}>Evangelism</option>
                                <option value="bible_study" {{ old('category', $question->category) === 'bible_study' ? 'selected' : '' }}>Bible Study</option>
                                <option value="community_service" {{ old('category', $question->category) === 'community_service' ? 'selected' : '' }}>Community Service</option>
                                <option value="other" {{ old('category', $question->category) === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('category')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="order">Display Order <span class="text-danger">*</span></label>
                            <input type="number" name="order" id="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', $question->order) }}" min="0" required>
                            <small class="form-text text-muted">Questions will be displayed in ascending order.</small>
                            @error('order')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', $question->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active (shown in weekly reports)</label>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Question
                        </button>
                        <a href="{{ route('small-groups.questions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
