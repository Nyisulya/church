@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-md-8">
            <h1 class="m-0 text-dark">📋 {{ __('My Care Requests') }}</h1>
            <p class="text-muted">{{ __('Track requests you have submitted to church leaders') }}</p>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('care-requests.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> {{ __('New Request') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if($careRequests->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted mb-4"></i>
                <h4 class="text-muted">{{ __('No care requests yet') }}</h4>
                <p class="text-muted">{{ __('When you need assistance, you can contact a church leader.') }}</p>
                <a href="{{ route('care-requests.create') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-plus mr-1"></i> {{ __('Contact a Leader') }}
                </a>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Subject') }}</th>
                            <th>{{ __('Category') }}</th>
                            <th>{{ __('Leader') }}</th>
                            <th>{{ __('Priority') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($careRequests as $request)
                            <tr>
                                <td>
                                    <small>{{ $request->created_at->format('M d, Y') }}</small><br>
                                    <small class="text-muted">{{ $request->created_at->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <strong>{{ Str::limit($request->subject, 30) }}</strong>
                                    @if($request->response)
                                        <br><small class="text-success"><i class="fas fa-reply"></i> {{ __('Responded') }}</small>
                                    @endif
                                </td>
                                <td>{{ $request->category_label }}</td>
                                <td>{{ $request->leader->name }}</td>
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
                                    <a href="{{ route('care-requests.show', $request) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($careRequests->hasPages())
                <div class="card-footer">
                    {{ $careRequests->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
