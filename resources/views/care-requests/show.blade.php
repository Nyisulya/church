@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-md-8">
            <h1 class="m-0 text-dark">📝 {{ __('Care Request Details') }}</h1>
        </div>
        <div class="col-md-4 text-right">
            @if(Auth::user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'department_leader']))
                <a href="{{ route('care-requests.leader-dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> {{ __('Back to Dashboard') }}
                </a>
            @else
                <a href="{{ route('care-requests.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> {{ __('Back to My Requests') }}
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="row">
        {{-- Main Request Details --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $careRequest->category_label }}</h5>
                    <div>
                        <span class="badge badge-{{ $careRequest->priority_badge['color'] }} mr-2">
                            {{ $careRequest->priority_badge['label'] }} {{ __('Priority') }}
                        </span>
                        <span class="badge badge-{{ $careRequest->status_badge['color'] }}">
                            {{ $careRequest->status_badge['label'] }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <h4>{{ $careRequest->subject }}</h4>
                    <hr>
                    <div class="message-content" style="white-space: pre-line; line-height: 1.8;">
                        {{ $careRequest->message }}
                    </div>
                </div>
                <div class="card-footer text-muted">
                    <small>
                        <i class="far fa-clock mr-1"></i> 
                        {{ __('Submitted') }}: {{ $careRequest->created_at->format('F d, Y \a\t h:i A') }}
                    </small>
                </div>
            </div>

            {{-- Response Section --}}
            @if($careRequest->response)
                <div class="card border-success mt-4">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-reply mr-2"></i>{{ __('Response from Leader') }}
                    </div>
                    <div class="card-body">
                        <div style="white-space: pre-line; line-height: 1.8;">
                            {{ $careRequest->response }}
                        </div>
                    </div>
                    <div class="card-footer text-muted">
                        <small>
                            <i class="far fa-user mr-1"></i> {{ $careRequest->leader->name }}<br>
                            <i class="far fa-clock mr-1"></i> {{ $careRequest->responded_at?->format('F d, Y \a\t h:i A') }}
                        </small>
                    </div>
                </div>
            @endif

            {{-- Leader Response Form (only visible to leaders) --}}
            @if(Auth::id() === $careRequest->leader_id || Auth::user()->hasRole('super_admin'))
                <div class="card mt-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-pen mr-2"></i>{{ __('Respond to Request') }}
                    </div>
                    <div class="card-body">
                        <form action="{{ route('care-requests.respond', $careRequest) }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label for="response"><strong>{{ __('Your Response') }}</strong> <span class="text-danger">*</span></label>
                                <textarea name="response" id="response" rows="4" 
                                          class="form-control @error('response') is-invalid @enderror" 
                                          placeholder="{{ __('Write your response to the member...') }}" 
                                          required>{{ old('response', $careRequest->response) }}</textarea>
                                @error('response')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="leader_notes"><strong>{{ __('Private Notes') }}</strong> <small class="text-muted">({{ __('Only visible to you') }})</small></label>
                                <textarea name="leader_notes" id="leader_notes" rows="2" 
                                          class="form-control" 
                                          placeholder="{{ __('Optional private notes...') }}">{{ old('leader_notes', $careRequest->leader_notes) }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="status"><strong>{{ __('Update Status') }}</strong></label>
                                <select name="status" id="status" class="form-control">
                                    @foreach(\App\Models\CareRequest::STATUSES as $value => $info)
                                        <option value="{{ $value }}" {{ $careRequest->status === $value ? 'selected' : '' }}>
                                            {{ __($info['label']) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane mr-2"></i>{{ __('Send Response') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar Info --}}
        <div class="col-md-4">
            {{-- Member Info --}}
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-user mr-2"></i>{{ __('Request From') }}
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3" 
                             style="width: 50px; height: 50px; font-size: 20px;">
                            {{ substr($careRequest->member->first_name, 0, 1) }}
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $careRequest->member->full_name }}</h5>
                            @if($careRequest->member->phone)
                                <small class="text-muted">{{ $careRequest->member->phone }}</small>
                            @endif
                        </div>
                    </div>
                    @if($careRequest->member->email)
                        <p class="mb-1"><i class="fas fa-envelope text-muted mr-2"></i>{{ $careRequest->member->email }}</p>
                    @endif
                    @if($careRequest->member->phone)
                        <p class="mb-1"><i class="fas fa-phone text-muted mr-2"></i>{{ $careRequest->member->phone }}</p>
                    @endif
                    @if($careRequest->member->address)
                        <p class="mb-0"><i class="fas fa-map-marker-alt text-muted mr-2"></i>{{ $careRequest->member->address }}</p>
                    @endif
                </div>
            </div>

            {{-- Leader Info --}}
            <div class="card mt-3">
                <div class="card-header">
                    <i class="fas fa-user-tie mr-2"></i>{{ __('Assigned To') }}
                </div>
                <div class="card-body">
                    <h5 class="mb-1">{{ $careRequest->leader->name }}</h5>
                    <small class="text-muted">{{ $careRequest->leader->email }}</small>
                </div>
            </div>

            {{-- Private Notes (Leader Only) --}}
            @if((Auth::id() === $careRequest->leader_id || Auth::user()->hasRole('super_admin')) && $careRequest->leader_notes)
                <div class="card mt-3 border-warning">
                    <div class="card-header bg-warning">
                        <i class="fas fa-lock mr-2"></i>{{ __('Private Notes') }}
                    </div>
                    <div class="card-body">
                        <small style="white-space: pre-line;">{{ $careRequest->leader_notes }}</small>
                    </div>
                </div>
            @endif

            {{-- Quick Status Update --}}
            @if(Auth::id() === $careRequest->leader_id || Auth::user()->hasRole('super_admin'))
                <div class="card mt-3">
                    <div class="card-header">
                        <i class="fas fa-sync-alt mr-2"></i>{{ __('Quick Status Update') }}
                    </div>
                    <div class="card-body">
                        <form action="{{ route('care-requests.update-status', $careRequest) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="btn-group-vertical w-100">
                                @foreach(\App\Models\CareRequest::STATUSES as $value => $info)
                                    <button type="submit" name="status" value="{{ $value }}" 
                                            class="btn btn-{{ $careRequest->status === $value ? $info['color'] : 'outline-'.$info['color'] }} mb-1">
                                        @if($careRequest->status === $value)
                                            <i class="fas fa-check mr-1"></i>
                                        @endif
                                        {{ __($info['label']) }}
                                    </button>
                                @endforeach
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
