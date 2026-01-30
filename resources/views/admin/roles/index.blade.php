@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Roles & Permissions</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Roles</li>
            </ol>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Manage Roles</h3>
            <div class="card-tools">
                <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Create New Role
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped projects">
                <thead>
                    <tr>
                        <th style="width: 20%">Role Name</th>
                        <th style="width: 50%">Permissions</th>
                        <th style="width: 30%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                    <tr>
                        <td>
                            <strong>{{ ucfirst($role->name) }}</strong>
                        </td>
                        <td>
                            @if($role->name === 'super_admin')
                                <span class="badge badge-success">All Permissions</span>
                            @else
                                @forelse($role->permissions->take(5) as $permission)
                                    <span class="badge badge-info">{{ $permission->name }}</span>
                                @empty
                                    <span class="text-muted small">No permissions assigned</span>
                                @endforelse
                                @if($role->permissions->count() > 5)
                                    <span class="badge badge-secondary">+{{ $role->permissions->count() - 5 }} more</span>
                                @endif
                            @endif
                        </td>
                        <td class="project-actions text-right">
                            @if($role->name !== 'super_admin')
                                <a class="btn btn-info btn-sm" href="{{ route('roles.edit', $role->id) }}">
                                    <i class="fas fa-pencil-alt"></i> Edit
                                </a>
                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this role?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-secondary btn-sm" disabled>
                                    <i class="fas fa-lock"></i> Protected
                                </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
