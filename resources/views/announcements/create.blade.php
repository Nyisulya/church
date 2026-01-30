@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Add New Announcement (Tangazo)</h3>
                </div>
                <form action="{{ route('announcements.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label>Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required placeholder="e.g., Youth Meeting / Mkutano wa Vijana">
                            @error('title')
                                <span class="text-danger text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Content (Maudhui) <span class="text-danger">*</span></label>
                            <textarea name="body" class="form-control" rows="6" required placeholder="Write announcement details in English and Kiswahili...">{{ old('body') }}</textarea>
                            @error('body')
                                <span class="text-danger text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Announcement Date <span class="text-danger">*</span></label>
                                    <input type="date" name="announcement_date" class="form-control" value="{{ old('announcement_date', date('Y-m-d')) }}" required>
                                    @error('announcement_date')
                                        <span class="text-danger text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Priority (0-10)</label>
                                    <input type="number" name="priority" class="form-control" value="{{ old('priority', 0) }}" min="0" max="10">
                                    <small class="text-muted">Higher priority shows first</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" checked>
                                <label class="custom-control-label" for="is_active">Active (Show in display)</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Announcement
                        </button>
                        <a href="{{ route('announcements.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
