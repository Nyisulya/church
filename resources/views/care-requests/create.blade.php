@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-md-8">
            <h1 class="m-0 text-dark">📬 {{ __('Contact a Leader') }}</h1>
            <p class="text-muted">{{ __('Submit a care request to a church leader') }}</p>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('care-requests.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> {{ __('My Requests') }}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-envelope mr-2"></i>{{ __('New Care Request') }}</h5>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('care-requests.store') }}" method="POST">
                        @csrf

                        {{-- Select Leader --}}
                        <div class="form-group">
                            <label for="leader_id"><strong>{{ __('Select Leader') }}</strong> <span class="text-danger">*</span></label>
                            <select name="leader_id" id="leader_id" class="form-control @error('leader_id') is-invalid @enderror" required>
                                <option value="">-- {{ __('Choose a leader') }} --</option>
                                @foreach($leaders as $leader)
                                    <option value="{{ $leader->id }}" {{ old('leader_id') == $leader->id ? 'selected' : '' }}>
                                        {{ $leader->name }}
                                        @if($leader->roles->isNotEmpty())
                                            ({{ $leader->roles->pluck('name')->map(fn($r) => ucwords(str_replace('_', ' ', $r)))->join(', ') }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('leader_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Category --}}
                        <div class="form-group">
                            <label for="category"><strong>{{ __('Type of Request') }}</strong> <span class="text-danger">*</span></label>
                            <select name="category" id="category" class="form-control @error('category') is-invalid @enderror" required>
                                @php
                                    $emojis = [
                                        'sick' => '🏥 ',
                                        'need_visit' => '🏠 ',
                                        'need_prayer' => '🙏 ',
                                        'counseling' => '💬 ',
                                        'financial_help' => '💰 ',
                                        'other' => '📝 ',
                                    ];
                                @endphp
                                @foreach($categories as $value => $label)
                                    <option value="{{ $value }}" {{ old('category') == $value ? 'selected' : '' }}>
                                        {{ $emojis[$value] ?? '' }}{{ __($label) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Priority --}}
                        <div class="form-group">
                            <label for="priority"><strong>{{ __('Priority') }}</strong> <span class="text-danger">*</span></label>
                            <select name="priority" id="priority" class="form-control @error('priority') is-invalid @enderror" required>
                                @foreach($priorities as $value => $info)
                                    <option value="{{ $value }}" {{ old('priority', 'normal') == $value ? 'selected' : '' }}>
                                        {{ __($info['label']) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Subject --}}
                        <div class="form-group">
                            <label for="subject"><strong>{{ __('Subject') }}</strong> <span class="text-danger">*</span></label>
                            <input type="text" name="subject" id="subject" 
                                   class="form-control @error('subject') is-invalid @enderror" 
                                   value="{{ old('subject') }}"
                                   placeholder="{{ __('Brief description of your request') }}" required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Message --}}
                        <div class="form-group">
                            <label for="message"><strong>{{ __('Message') }}</strong> <span class="text-danger">*</span></label>
                            <textarea name="message" id="message" rows="5" 
                                      class="form-control @error('message') is-invalid @enderror" 
                                      placeholder="{{ __('Provide details about your request...') }}" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane mr-2"></i>{{ __('Submit Request') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Help Sidebar --}}
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h5><i class="fas fa-info-circle text-info mr-2"></i>{{ __('How it Works') }}</h5>
                    <hr>
                    <p class="small">
                        <strong>1.</strong> {{ __('Select the leader you want to contact') }}<br>
                        <strong>2.</strong> {{ __('Choose the type of request') }}<br>
                        <strong>3.</strong> {{ __('Describe your situation in detail') }}<br>
                        <strong>4.</strong> {{ __('Submit your request') }}
                    </p>
                    <hr>
                    <p class="small mb-0">
                        <i class="fas fa-bell text-warning"></i> 
                        {{ __('The leader will be notified immediately and you will receive a notification when they respond.') }}
                    </p>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <i class="fas fa-list mr-2"></i>{{ __('Request Types') }}
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($categories as $value => $label)
                            <li class="list-group-item small">{{ $emojis[$value] ?? '' }}{{ __($label) }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
