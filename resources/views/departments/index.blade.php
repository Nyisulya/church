@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">{{ __('Ministries & Departments') }}</h1>
        </div>
    </div>
    
    @if(Auth::user()->hasAnyRole(['super_admin', 'admin', 'pastor']))
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('departments.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('Add New Ministry') }}
            </a>
        </div>
    </div>
    @endif

    <div class="row">
        @foreach($departments as $department)
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $department->name }}</h3>
                    @if(Auth::user()->hasAnyRole(['super_admin', 'admin', 'pastor']))
                    <div class="card-tools">
                        <a href="{{ route('departments.edit', $department) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('departments.destroy', $department) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ Str::limit($department->description, 100) }}</p>
                    
                    @if($department->chairman || $department->secretary)
                    <div class="mb-2">
                        @if($department->chairman)
                        <small class="d-block"><strong>{{ __('Chairman/Chairwoman') }}:</strong> {{ $department->chairman->full_name }}</small>
                        @endif
                        @if($department->secretary)
                        <small class="d-block"><strong>{{ __('Secretary') }}:</strong> {{ $department->secretary->full_name }}</small>
                        @endif
                    </div>
                    @endif
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge badge-primary">{{ $department->members_count }} {{ __('Members') }}</span>
                        <a href="{{ route('departments.show', $department) }}" class="btn btn-sm btn-outline-primary">
                            {{ __('View Details') }} →
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
