@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">📬 {{ __('My Messages') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Inbox') }}</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="80"></th>
                                <th>{{ __('From') }}</th>
                                <th>{{ __('Subject') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th width="100">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($notifications as $notification)
                            <tr class="{{ $notification->read_at ? '' : 'bg-light font-weight-bold' }}">
                                <td>
                                    @if(!$notification->read_at)
                                    <span class="badge badge-danger">{{ __('New') }}</span>
                                    @endif
                                </td>
                                <td>{{ $notification->data['sender'] ?? __('System') }}</td>
                                <td>{{ $notification->data['subject'] ?? __('No Subject') }}</td>
                                <td>{{ $notification->created_at->diffForHumans() }}</td>
                                <td>
                                    <a href="{{ route('inbox.show', $notification->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> {{ __('Read') }}
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center p-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i><br>
                                    {{ __('No messages yet.') }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
