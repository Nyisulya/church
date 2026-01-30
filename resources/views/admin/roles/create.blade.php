@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Create New Role</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </div>
    </div>

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Role Details</h3>
        </div>
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="name">Role Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Enter role name (e.g. Editor)" value="{{ old('name') }}" required>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Assign Permissions</label>
                    <div class="row">
                        @foreach($permissions as $group => $perms)
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">{{ $group }}</h5>
                                </div>
                                <div class="card-body">
                                    @foreach($perms as $permission)
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="perm_{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}">
                                        <label for="perm_{{ $permission->id }}" class="custom-control-label font-weight-normal">{{ $permission->name }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Create Role</button>
                <a href="{{ route('roles.index') }}" class="btn btn-default float-right">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
