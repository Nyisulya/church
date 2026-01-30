@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus"></i> Create New Pledge
                    </h3>
                </div>
                <form action="{{ route('pledges.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label>Member <span class="text-danger">*</span></label>
                            <select name="member_id" class="form-control @error('member_id') is-invalid @enderror" required>
                                <option value="">Select Member</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                                        {{ $member->full_name }} ({{ $member->member_number }})
                                    </option>
                                @endforeach
                            </select>
                            @error('member_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Purpose/Project <span class="text-danger">*</span></label>
                            <select name="purpose" class="form-control @error('purpose') is-invalid @enderror" required>
                                <option value="">Select Purpose/Project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project }}" {{ old('purpose') == $project ? 'selected' : '' }}>
                                        {{ $project }}
                                    </option>
                                @endforeach
                            </select>
                            @error('purpose')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Pledge Amount <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" 
                                   step="0.01" min="1" placeholder="Enter amount" 
                                   value="{{ old('amount') }}" required>
                            @error('amount')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Start Date <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" 
                                           value="{{ old('start_date', date('Y-m-d')) }}" required>
                                    @error('start_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>End Date <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" 
                                           value="{{ old('end_date') }}" required>
                                    @error('end_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Pledge
                        </button>
                        <a href="{{ route('pledges.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
