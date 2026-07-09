@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mt-3">
        <div class="col-md-8">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Edit Contribution') }}</h3>
                </div>
                <form action="{{ route('contributions.update', $contribution) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label>{{ __('Member') }} <span class="text-danger">*</span></label>
                            <input type="text" id="member_search" class="form-control mb-2" placeholder="🔍 Anza kuandika jina au namba ya muumini..." autocomplete="off">
                            <select name="member_id" id="member_id_select" class="form-control" required>
                                <option value="">{{ __('Select Member') }}</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" {{ old('member_id', $contribution->member_id) == $member->id ? 'selected' : '' }}>
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
                                        <input type="number" name="amount" class="form-control" step="0.01" min="0" value="{{ old('amount', $contribution->amount) }}" required>
                                    </div>
                                    @error('amount')
                                        <span class="text-danger text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Date') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="date" class="form-control" value="{{ old('date', $contribution->date->format('Y-m-d')) }}" required>
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
                                        <option value="zaka" {{ old('type', $contribution->type) == 'zaka' ? 'selected' : '' }}>{{ __('Zaka (Tithe)') }}</option>
                                        <option value="sadaka" {{ old('type', $contribution->type) == 'sadaka' ? 'selected' : '' }}>{{ __('Sadaka (Offering)') }}</option>
                                        <option value="project" {{ old('type', $contribution->type) == 'project' ? 'selected' : '' }}>{{ __('Project') }}</option>
                                        <option value="building" {{ old('type', $contribution->type) == 'building' ? 'selected' : '' }}>{{ __('Building Fund') }}</option>
                                        <option value="thanksgiving" {{ old('type', $contribution->type) == 'thanksgiving' ? 'selected' : '' }}>{{ __('Thanksgiving') }}</option>
                                        <option value="other" {{ old('type', $contribution->type) == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
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
                                        <option value="cash" {{ old('payment_method', $contribution->payment_method) == 'cash' ? 'selected' : '' }}>{{ __('Cash') }}</option>
                                        <option value="mpesa" {{ old('payment_method', $contribution->payment_method) == 'mpesa' ? 'selected' : '' }}>{{ __('M-Pesa') }}</option>
                                        <option value="bank" {{ old('payment_method', $contribution->payment_method) == 'bank' ? 'selected' : '' }}>{{ __('Bank Transfer') }}</option>
                                        <option value="check" {{ old('payment_method', $contribution->payment_method) == 'check' ? 'selected' : '' }}>{{ __('Check') }}</option>
                                    </select>
                                    @error('payment_method')
                                        <span class="text-danger text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ __('Reference Number (Optional)') }}</label>
                            <input type="text" name="reference_number" class="form-control" placeholder="{{ __('e.g. M-Pesa Code, Check No.') }}" value="{{ old('reference_number', $contribution->reference_number) }}">
                        </div>

                        <div class="form-group">
                            <label>{{ __('Notes (Optional)') }}</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $contribution->notes) }}</textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> {{ __('Update Contribution') }}
                        </button>
                        <a href="{{ route('contributions.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('member_search');
    var select = document.getElementById('member_id_select');
    if (!searchInput || !select) return;

    var originalOptions = [];
    for (var i = 0; i < select.options.length; i++) {
        originalOptions.push({
            value: select.options[i].value,
            text: select.options[i].text,
            selected: select.options[i].selected
        });
    }

    searchInput.addEventListener('input', function() {
        var filter = this.value.toLowerCase();
        var currentValue = select.value;
        select.innerHTML = '';

        for (var j = 0; j < originalOptions.length; j++) {
            var opt = originalOptions[j];
            if (opt.value === "" || opt.text.toLowerCase().indexOf(filter) !== -1) {
                var o = document.createElement('option');
                o.value = opt.value;
                o.text = opt.text;
                if (opt.value === currentValue) o.selected = true;
                select.appendChild(o);
            }
        }
    });
});
</script>
@endsection

