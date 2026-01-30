@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">{{ __('Edit Ministry') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Ministry Details') }}</h3>
                </div>
                <form action="{{ route('departments.update', $department) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label>{{ __('Ministry Name') }}</label>
                            <input type="text" name="name" class="form-control" required value="{{ $department->name }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __('Description') }}</label>
                            <textarea name="description" class="form-control" rows="4">{{ $department->description }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>{{ __('Chairman/Chairwoman') }}</label>
                            <select name="chairman_id" class="form-control">
                                <option value="">-- {{ __('Select Option') }} --</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" {{ $department->chairman_id == $member->id ? 'selected' : '' }}>
                                        {{ $member->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ __('Optional') }}</small>
                        </div>

                        <div class="form-group">
                            <label>{{ __('Secretary') }}</label>
                            <select name="secretary_id" class="form-control">
                                <option value="">-- {{ __('Select Option') }} --</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" {{ $department->secretary_id == $member->id ? 'selected' : '' }}>
                                        {{ $member->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ __('Optional') }}</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">{{ __('Update Ministry') }}</button>
                        <a href="{{ route('departments.index') }}" class="btn btn-default float-right">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
