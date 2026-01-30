@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <a href="{{ route('inbox.index') }}" class="btn btn-default mb-3">
                <i class="fas fa-arrow-left"></i> {{ __('Back to Messages') }}
            </a>
            <h1 class="m-0 text-dark">{{ $notification->data['subject'] ?? __('Message') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('From') }}: {{ $notification->data['sender'] ?? __('System') }}</h3>
                    <div class="card-tools">
                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                </div>
                <div class="card-body">
                    <p style="white-space: pre-wrap;">{{ $notification->data['message'] }}</p>
                </div>
                <div class="card-footer">
                    <a href="{{ route('inbox.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> {{ __('Back to Inbox') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
