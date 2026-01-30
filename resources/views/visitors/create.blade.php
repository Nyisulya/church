@extends('layouts.admin')

@section('title', __('Record Visitor'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('Record New Visitor') }}</h1>
        <a href="{{ route('visitors.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-2"></i> {{ __('Back to List') }}
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('visitors.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('First Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" required value="{{ old('first_name') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('Last Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" required value="{{ old('last_name') }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('Phone Number') }}</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('Email Address') }}</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('Visit Date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="visit_date" class="form-control" required value="{{ old('visit_date', date('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('How did they find us?') }}</label>
                            <select name="how_found_us" class="form-control">
                                <option value="">{{ __('Select Option') }}</option>
                                <option value="Friend/Family">{{ __('Friend/Family') }}</option>
                                <option value="Social Media">{{ __('Social Media') }}</option>
                                <option value="Website">{{ __('Website') }}</option>
                                <option value="Signage">{{ __('Signage') }}</option>
                                <option value="Other">{{ __('Other') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>{{ __('Assign Follow-up To') }}</label>
                    <select name="assigned_to_member_id" class="form-control select2">
                        <option value="">{{ __('Select Member') }}</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>{{ __('Notes / Prayer Requests') }}</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i> {{ __('Save Visitor') }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
