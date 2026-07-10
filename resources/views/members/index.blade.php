@extends('layouts.admin')

@section('content')
<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3"><i class="fas fa-users"></i> 
                @if(Auth::user()->hasRole('member') && !Auth::user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'treasurer', 'department_leader']))
                    {{ __('My Profile') }}
                @else
                    {{ __('Members Management') }}
                @endif
            </h1>
        </div>
        <div class="col-md-6 text-right">
            @can('create', App\Models\Member::class)
            <a href="{{ route('members.import') }}" class="btn btn-success mr-2">
                <i class="fas fa-file-upload"></i> {{ __('Import Members') }}
            </a>
            <a href="{{ route('members.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('Add New Member') }}
            </a>
            @endcan
        </div>
    </div>

    @if($isRegularMember ?? false)
        {{-- Regular Member View - Only show their own info --}}
        @if($member ?? null)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('My Information') }}</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th width="30%">{{ __('Member Number') }}</th>
                            <td>{{ $member->member_number }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Full Name') }}</th>
                            <td>{{ $member->full_name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Gender') }}</th>
                            <td>{{ $member->gender ? ucfirst(__($member->gender)) : __('Not provided') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Email') }}</th>
                            <td>{{ $member->email }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Phone') }}</th>
                            <td>{{ $member->phone ?? __('Not provided') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Date of Birth') }}</th>
                            <td>{{ $member->date_of_birth ? $member->date_of_birth->format('F d, Y') : __('Not provided') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Marital Status') }}</th>
                            <td>{{ $member->marital_status ? ucfirst(__($member->marital_status)) : __('Not provided') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Address') }}</th>
                            <td>{{ $member->address ?? __('Not provided') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Departments') }}</th>
                            <td>
                                @if($member->departments->count())
                                    @foreach($member->departments as $dept)
                                        <span class="badge badge-info mr-1">{{ $dept->name }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">{{ __('No department') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Status') }}</th>
                            <td>
                                @if($member->status == 'active')
                                    <span class="badge badge-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ route('members.edit', $member) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> {{ __('Edit My Profile') }}
                </a>
            </div>
        </div>
        @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> {{ __('Your account is not linked to a member profile. Please contact the administrator.') }}
        </div>
        @endif

    @else
        {{-- Admin/Staff View - Full member list with filters --}}
    <div class="card card-default mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('members.index') }}">
                <div class="row">
                    <div class="col-md-3 form-group">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search members...') }}" class="form-control" />
                    </div>
                    <div class="col-md-2 form-group">
                        <select name="status" class="form-control">
                            <option value="">{{ __('All Status') }}</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <select name="gender" class="form-control">
                            <option value="">{{ __('All Genders') }}</option>
                            <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                            <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <select name="registration_type" class="form-control">
                            <option value="">{{ __('Aina Zote') }}</option>
                            <option value="Mshiriki Rasmi" {{ request('registration_type') == 'Mshiriki Rasmi' ? 'selected' : '' }}>{{ __('Mshiriki Rasmi') }}</option>
                            <option value="Muumini wa Kawaida" {{ request('registration_type') == 'Muumini wa Kawaida' ? 'selected' : '' }}>{{ __('Muumini wa Kawaida') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> {{ __('Filter') }}</button>
                        <a href="{{ route('members.index') }}" class="btn btn-default">{{ __('Clear') }}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Members Table --}}
    @if($members->count())
    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ __('Member') }}</th>
                        <th>{{ __('Contact') }}</th>
                        <th>{{ __('Department(s)') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($members as $member)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center mr-2" style="width: 40px; height: 40px;">
                                    <span class="text-white font-weight-bold">{{ substr($member->full_name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <strong>{{ $member->full_name }}</strong>
                                    @if($member->registration_type == 'Mshiriki Rasmi')
                                        <span class="badge badge-success ml-1" style="font-size: 0.7rem;">{{ __('Mshiriki Rasmi') }}</span>
                                    @elseif($member->registration_type == 'Muumini wa Kawaida')
                                        <span class="badge badge-info ml-1" style="font-size: 0.7rem;">{{ __('Muumini') }}</span>
                                    @endif
                                    <br>
                                    <small class="text-muted">{{ ucfirst(__($member->gender ?? 'N/A')) }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            {{ $member->email ?? 'N/A' }}<br>
                            <small class="text-muted">{{ $member->phone ?? 'N/A' }}</small>
                        </td>
                        <td>
                            @if($member->departments->count())
                                @foreach($member->departments->take(2) as $dept)
                                    <span class="badge badge-info mr-1">{{ $dept->name }}</span>
                                @endforeach
                                @if($member->departments->count() > 2)
                                    <small class="text-muted">+{{ $member->departments->count() - 2 }} {{ __('more') }}</small>
                                @endif
                            @else
                                <span class="text-muted">{{ __('No department') }}</span>
                            @endif
                        </td>
                        <td>
                            @if($member->status == 'active')
                                <span class="badge badge-success">{{ __('Active') }}</span>
                            @else
                                <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('members.show', $member) }}" class="btn btn-sm btn-info" title="{{ __('View') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('update', $member)
                                <a href="{{ route('members.edit', $member) }}" class="btn btn-sm btn-primary" title="{{ __('Edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('delete', $member)
                                <form action="{{ route('members.destroy', $member) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this member?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="{{ __('Delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">
        {{ $members->links() }}
    </div>
    @else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-users fa-4x text-muted mb-3"></i>
            <h3 class="mt-2">{{ __('No members found') }}</h3>
            <p class="text-muted">{{ __('Get started by adding a new member.') }}</p>
            @can('create', App\Models\Member::class)
            <div class="mt-4">
                <a href="{{ route('members.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('Add New Member') }}
                </a>
            </div>
            @endcan
        </div>
    </div>
    @endif
    @endif
</div>
@endsection
