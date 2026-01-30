@extends('layouts.admin')

@section('title', 'Visitor Tracking')

@section('content')
<div class="container-fluid">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">👥 {{ __('Visitor Tracking') }}</h1>
            <p class="text-muted small mb-0">{{ __('Manage and follow up with guests') }}</p>
        </div>
        <a href="{{ route('visitors.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus mr-2"></i> {{ __('Record Visitor') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('Total Visitors') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ __('Pending Follow-up') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">{{ __('Converted to Members') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['converted'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Visitors List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('All Visitors') }}</h6>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-toggle="dropdown">
                    {{ __('Filter Status') }}
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('visitors.index') }}">{{ __('All') }}</a>
                    <a class="dropdown-item" href="{{ route('visitors.index', ['status' => 'pending']) }}">{{ __('Pending') }}</a>
                    <a class="dropdown-item" href="{{ route('visitors.index', ['status' => 'contacted']) }}">{{ __('Contacted') }}</a>
                    <a class="dropdown-item" href="{{ route('visitors.index', ['status' => 'member']) }}">{{ __('Converted') }}</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Visit Date') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('Assigned To') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($visitors as $visitor)
                        <tr>
                            <td>
                                <a href="{{ route('visitors.show', $visitor) }}" class="font-weight-bold">
                                    {{ $visitor->full_name }}
                                </a>
                            </td>
                            <td>{{ $visitor->visit_date->format('M d, Y') }}</td>
                            <td>{{ $visitor->phone ?? '-' }}</td>
                            <td>
                                @if($visitor->assignedTo)
                                    <a href="{{ route('members.show', $visitor->assignedTo) }}">
                                        {{ $visitor->assignedTo->full_name }}
                                    </a>
                                @else
                                    <span class="text-muted font-italic">{{ __('Unassigned') }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $visitor->follow_up_status === 'pending' ? 'warning' : ($visitor->follow_up_status === 'member' ? 'success' : 'info') }}">
                                    {{ ucfirst(__($visitor->follow_up_status)) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('visitors.show', $visitor) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('visitors.edit', $visitor) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">{{ __('No visitors found.') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $visitors->links() }}
        </div>
    </div>

</div>
@endsection
