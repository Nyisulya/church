@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mt-3">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Record New Contribution') }}</h3>
                </div>
                <form action="{{ route('contributions.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label>{{ __('Member') }} <span class="text-danger">*</span></label>
                            <select name="member_id" class="form-control select2" required>
                                <option value="">{{ __('Select Member') }}</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                                        {{ $member->full_name }} ({{ $member->member_number }})
                                    </option>
                                @endforeach
                            </select>
                            @error('member_id')
                                <span class="text-danger text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Amount') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">{{ $currencySymbol }}</span>
                                        </div>
                                        <input type="number" name="amount" class="form-control" step="0.01" min="0" value="{{ old('amount') }}" required>
                                    </div>
                                    @error('amount')
                                        <span class="text-danger text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Date') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                                    @error('date')
                                        <span class="text-danger text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Type') }} <span class="text-danger">*</span></label>
                                    <select name="type" class="form-control" required>
                                        <option value="zaka" {{ old('type') == 'zaka' ? 'selected' : '' }}>{{ __('Zaka (Tithe)') }}</option>
                                        <option value="sadaka" {{ old('type') == 'sadaka' ? 'selected' : '' }}>{{ __('Sadaka (Offering)') }}</option>
                                        <option value="project" {{ old('type') == 'project' ? 'selected' : '' }}>{{ __('Project') }}</option>
                                        <option value="building" {{ old('type') == 'building' ? 'selected' : '' }}>{{ __('Building Fund') }}</option>
                                        <option value="thanksgiving" {{ old('type') == 'thanksgiving' ? 'selected' : '' }}>{{ __('Thanksgiving') }}</option>
                                        <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                                    </select>
                                    @error('type')
                                        <span class="text-danger text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Payment Method') }} <span class="text-danger">*</span></label>
                                    <select name="payment_method" class="form-control" required>
                                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>{{ __('Cash') }}</option>
                                        <option value="mpesa" {{ old('payment_method') == 'mpesa' ? 'selected' : '' }}>{{ __('M-Pesa') }}</option>
                                        <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>{{ __('Bank Transfer') }}</option>
                                        <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>{{ __('Check') }}</option>
                                    </select>
                                    @error('payment_method')
                                        <span class="text-danger text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ __('Reference Number (Optional)') }}</label>
                            <input type="text" name="reference_number" class="form-control" placeholder="{{ __('e.g. M-Pesa Code, Check No.') }}" value="{{ old('reference_number') }}">
                        </div>

                        <div class="form-group">
                            <label>{{ __('Notes (Optional)') }}</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('Record Contribution') }}
                        </button>
                        <a href="{{ route('contributions.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid #ced4da !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
            padding-left: 12px !important;
            color: #495057 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "{{ __('Select Member') }}",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endpush
