@extends('layouts.admin')

@section('title', 'Visitor Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('Visitor Details') }}</h1>
        <div>
            <a href="{{ route('visitors.edit', $visitor) }}" class="btn btn-primary mr-2">
                <i class="fas fa-edit mr-2"></i> {{ __('Edit') }}
            </a>
            <a href="{{ route('visitors.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-2"></i> {{ __('Back') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Personal Information') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 font-weight-bold">{{ __('Full Name') }}:</div>
                        <div class="col-sm-8">{{ $visitor->full_name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 font-weight-bold">{{ __('Phone') }}:</div>
                        <div class="col-sm-8">{{ $visitor->phone ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 font-weight-bold">{{ __('Email') }}:</div>
                        <div class="col-sm-8">{{ $visitor->email ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 font-weight-bold">{{ __('First Visit') }}:</div>
                        <div class="col-sm-8">{{ $visitor->visit_date->format('F j, Y') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 font-weight-bold">{{ __('Found Us Via') }}:</div>
                        <div class="col-sm-8">{{ $visitor->how_found_us ?? 'N/A' }}</div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-sm-4 font-weight-bold">{{ __('Notes') }}:</div>
                        <div class="col-sm-8">{{ $visitor->notes ?? __('No notes recorded.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Follow-up Status') }}</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4 text-center">
                        <span class="badge badge-{{ $visitor->follow_up_status === 'pending' ? 'warning' : ($visitor->follow_up_status === 'member' ? 'success' : 'info') }} p-2" style="font-size: 1.2em;">
                            {{ ucfirst(__($visitor->follow_up_status)) }}
                        </span>
                    </div>

                    <form action="{{ route('visitors.update', $visitor) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="first_name" value="{{ $visitor->first_name }}">
                        <input type="hidden" name="last_name" value="{{ $visitor->last_name }}">
                        <input type="hidden" name="visit_date" value="{{ $visitor->visit_date->format('Y-m-d') }}">
                        
                        <div class="form-group">
                            <label class="font-weight-bold">{{ __('Update Status') }}</label>
                            <select name="follow_up_status" class="form-control" onchange="this.form.submit()">
                                <option value="pending" {{ $visitor->follow_up_status == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>>
                                <option value="contacted" {{ $visitor->follow_up_status == 'contacted' ? 'selected' : '' }}>{{ __('Contacted') }}</option>>
                                <option value="member" {{ $visitor->follow_up_status == 'member' ? 'selected' : '' }}>{{ __('Converted to Member') }}</option>>
                                <option value="dropped" {{ $visitor->follow_up_status == 'dropped' ? 'selected' : '' }}>{{ __('Dropped') }}</option>>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">{{ __('Assigned To') }}</label>
                            <select name="assigned_to_member_id" class="form-control select2" onchange="this.form.submit()">
                                <option value="">{{ __('Unassigned') }}</option>>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" {{ $visitor->assigned_to_member_id == $member->id ? 'selected' : '' }}>
                                        {{ $member->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
