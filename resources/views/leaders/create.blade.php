@extends('layouts.admin')

@section('title', __('Add Leader'))

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">{{ __('Add New Leader') }}</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('leaders.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="member_id">{{ __('Select Member') }}</label>
                    <select name="member_id" id="member_id" class="form-control select2" required>
                        <option value="">{{ __('-- Select Member --') }}</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}">{{ $member->full_name }} ({{ $member->member_number }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="type">{{ __('Leadership Type') }}</label>
                    <select name="type" id="type" class="form-control" required onchange="toggleTypeFields()">
                        <option value="">{{ __('-- Select Type --') }}</option>
                        <option value="system">{{ __('System Role (Global)') }}</option>
                        <option value="department">{{ __('Department Leader') }}</option>
                    </select>
                </div>

                <div id="system_fields" style="display: none;">
                    <div class="form-group">
                        <label for="role_id">{{ __('System Role') }}</label>
                        <select name="role_id" id="role_id" class="form-control">
                            <option value="">{{ __('-- Select Role --') }}</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            {{ __('Assigns a system-wide role. The member must have a user account.') }}
                        </small>
                    </div>
                </div>

                <div id="department_fields" style="display: none;">
                    <div class="form-group">
                        <label for="department_id">{{ __('Department') }}</label>
                        <select name="department_id" id="department_id" class="form-control">
                            <option value="">{{ __('-- Select Department --') }}</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            {{ __('Assigns the member as a leader of the selected department.') }}
                        </small>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">{{ __('Assign Leadership') }}</button>
                    <a href="{{ route('leaders.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleTypeFields() {
        const type = document.getElementById('type').value;
        const systemFields = document.getElementById('system_fields');
        const departmentFields = document.getElementById('department_fields');
        const roleSelect = document.getElementById('role_id');
        const deptSelect = document.getElementById('department_id');

        if (type === 'system') {
            systemFields.style.display = 'block';
            departmentFields.style.display = 'none';
            roleSelect.required = true;
            deptSelect.required = false;
        } else if (type === 'department') {
            systemFields.style.display = 'none';
            departmentFields.style.display = 'block';
            roleSelect.required = false;
            deptSelect.required = true;
        } else {
            systemFields.style.display = 'none';
            departmentFields.style.display = 'none';
            roleSelect.required = false;
            deptSelect.required = false;
        }
    }
</script>
@endpush
@endsection
