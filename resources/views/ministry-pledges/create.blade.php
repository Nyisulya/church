@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">{{ __('Create Ministry Pledge') }}</h1>
            <p class="text-muted">{{ $department->name }}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Pledge Details') }}</h3>
                </div>
                <form action="{{ route('ministry-pledges.store', $department) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label>{{ __('Title') }} *</label>
                            <input type="text" name="title" class="form-control" required 
                                   placeholder="{{ __('e.g. Youth Camp Fundraiser') }}" value="{{ old('title') }}">
                        </div>

                        <div class="form-group">
                            <label>{{ __('Description') }}</label>
                            <textarea name="description" class="form-control" rows="4" 
                                      placeholder="{{ __('Optional description') }}">{{ old('description') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>{{ __('Target Amount') }} (TZS) *</label>
                            <input type="number" name="target_amount" class="form-control" required 
                                   min="0" step="0.01" value="{{ old('target_amount') }}">
                        </div>

                        <div class="form-group">
                            <label>{{ __('Target Date') }}</label>
                            <input type="date" name="target_date" class="form-control" value="{{ old('target_date') }}">
                            <small class="text-muted">{{ __('Optional') }}</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('Create Pledge') }}
                        </button>
                        <a href="{{ route('departments.show', $department) }}" class="btn btn-default float-right">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
