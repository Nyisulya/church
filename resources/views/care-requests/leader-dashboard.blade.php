@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">📥 {{ __('Care Requests Dashboard') }}</h1>
            <p class="text-muted">{{ __('Manage care requests from church members') }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['pending'] }}</h3>
                    <p>{{ __('Pending Requests') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['in_progress'] }}</h3>
                    <p>{{ __('In Progress') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-spinner"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['completed'] }}</h3>
                    <p>{{ __('Completed') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Requests Table --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list mr-2"></i>{{ __('All Requests') }}</h5>
        </div>
        <div class="card-body p-0">
            @if($careRequests->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">{{ __('No care requests yet') }}</h4>
                    <p class="text-muted">{{ __('When members send you requests, they will appear here.') }}</p>
                </div>
            @else
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Member') }}</th>
                            <th>{{ __('Subject') }}</th>
                            <th>{{ __('Category') }}</th>
                            <th>{{ __('Priority') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($careRequests as $request)
                            <tr class="{{ $request->status === 'pending' ? 'table-warning' : '' }}">
                                <td>
                                    <small>{{ $request->created_at->format('M d, Y') }}</small><br>
                                    <small class="text-muted">{{ $request->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <strong>{{ $request->member->full_name }}</strong>
                                    @if($request->member->phone)
                                        <br><small class="text-muted">{{ $request->member->phone }}</small>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ Str::limit($request->subject, 30) }}</strong>
                                    @if($request->priority === 'urgent')
                                        <i class="fas fa-exclamation-circle text-danger ml-1" title="{{ __('Urgent') }}"></i>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $request->category_label }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $request->priority_badge['color'] }}">
                                        {{ $request->priority_badge['label'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $request->status_badge['color'] }}">
                                        {{ $request->status_badge['label'] }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('care-requests.show', $request) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye mr-1"></i>{{ __('View') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        @if($careRequests->hasPages())
            <div class="card-footer">
                {{ $careRequests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
