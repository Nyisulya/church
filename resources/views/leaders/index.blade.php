@extends('layouts.admin')

@section('title', __('Church Leaders'))

@section('content')
<div class="container-fluid">
    
    <!-- Header & Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ __('Church Leadership') }}</h1>
            <p class="text-muted small mb-0">{{ __('Manage system roles and department leaders') }}</p>
        </div>
        <div>
            <a href="{{ route('reports.communication.index', ['group' => 'leaders']) }}" class="btn btn-info shadow-sm mr-2">
                <i class="fas fa-envelope fa-sm text-white-50 mr-2"></i> {{ __('Email All') }}
            </a>
            <div class="btn-group shadow-sm mr-2">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-download fa-sm text-white-50 mr-2"></i> {{ __('Export') }}
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('leaders.export') }}">
                        <i class="fas fa-file-pdf text-danger mr-2"></i> {{ __('Export PDF') }}
                    </a>
                    <a class="dropdown-item" href="{{ route('leaders.export', ['format' => 'csv']) }}">
                        <i class="fas fa-file-csv text-success mr-2"></i> {{ __('Export CSV') }}
                    </a>
                </div>
            </div>
            <a href="{{ route('leaders.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50 mr-2"></i> {{ __('Add New Leader') }}
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('Total Leadership Positions') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">{{ __('Unique Leaders') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['unique_leaders'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">{{ __('System Roles (Admin/Pastor)') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['system_leaders'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ __('Department Leaders') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['department_leaders'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-church fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="card shadow mb-4">
        <div class="card-body py-3">
            <form action="{{ route('leaders.index') }}" method="GET" class="form-inline justify-content-between">
                <div class="input-group mb-2 mr-sm-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text bg-light border-0"><i class="fas fa-search text-gray-500"></i></div>
                    </div>
                    <input type="text" class="form-control bg-light border-0 small" name="search" placeholder="{{ __('Search by name...') }}" value="{{ request('search') }}">
                </div>
                
                <div class="input-group mb-2 mr-sm-2">
                    <select name="type" class="custom-select bg-light border-0" onchange="this.form.submit()">
                        <option value="">{{ __('All Leadership Types') }}</option>
                        <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>{{ __('System Roles Only') }}</option>
                        <option value="department" {{ request('type') == 'department' ? 'selected' : '' }}>{{ __('Department Leaders Only') }}</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary mb-2 btn-sm">{{ __('Filter') }}</button>
                @if(request()->hasAny(['search', 'type']))
                    <a href="{{ route('leaders.index') }}" class="btn btn-secondary mb-2 ml-2 btn-sm">{{ __('Clear') }}</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Leaders Grid -->
    <div class="row">
        @forelse($leaders as $leader)
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 border-0">
                <div class="card-body text-center">
                    <!-- Profile Photo -->
                    <div class="mb-3">
                        @if($leader['member']->profile_photo)
                            <img src="{{ Storage::url($leader['member']->profile_photo) }}" alt="" class="rounded-circle shadow-sm" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #fff;">
                        @else
                            <div class="rounded-circle bg-gradient-primary text-white d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 100px; height: 100px; font-size: 2.5rem; border: 3px solid #fff;">
                                {{ substr($leader['member']->full_name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    
                    <!-- Name & Role -->
                    <h5 class="font-weight-bold text-gray-800 mb-1">
                        <a href="{{ route('members.show', $leader['member']->id) }}" class="text-decoration-none text-gray-800 hover:text-primary">
                            {{ $leader['member']->full_name }}
                        </a>
                    </h5>
                    <p class="text-primary font-weight-bold mb-1">{{ $leader['role_name'] }}</p>
                    <p class="text-muted small mb-3">
                        @if($leader['is_system'])
                            <span class="badge badge-light border"><i class="fas fa-globe mr-1"></i> {{ $leader['context'] }}</span>
                        @else
                            <span class="badge badge-light border"><i class="fas fa-users mr-1"></i> {{ $leader['context'] }}</span>
                        @endif
                    </p>

                    <!-- Actions -->
                    <div class="d-flex justify-content-center mt-3">
                        <a href="mailto:{{ $leader['member']->email }}" class="btn btn-sm btn-outline-primary mr-2 rounded-pill" title="{{ __('Send Email') }}">
                            <i class="fas fa-envelope"></i>
                        </a>
                        <a href="tel:{{ $leader['member']->phone }}" class="btn btn-sm btn-outline-success mr-2 rounded-pill" title="{{ __('Call') }}">
                            <i class="fas fa-phone"></i>
                        </a>
                        
                        <form action="{{ route('leaders.remove') }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to remove this leadership role?') }}');" style="display: inline;">
                            @csrf
                            <input type="hidden" name="member_id" value="{{ $leader['member']->id }}">
                            <input type="hidden" name="context_type" value="{{ $leader['is_system'] ? 'system' : 'department' }}">
                            <input type="hidden" name="context_id" value="{{ $leader['is_system'] ? $leader['role_slug'] : $leader['department_id'] }}">
                            
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" title="{{ __('Remove Role') }}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="text-gray-400 mb-3">
                <i class="fas fa-user-slash fa-4x"></i>
            </div>
            <h4 class="text-gray-600">{{ __('No leaders found') }}</h4>
            <p class="text-gray-500">{{ __('Try adjusting your search or filters.') }}</p>
        </div>
        @endforelse
    </div>

</div>
@endsection
