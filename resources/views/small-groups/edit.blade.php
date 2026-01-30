@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">Edit Small Group</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Group Details</h3>
                </div>
                <form action="{{ route('small-groups.update', $smallGroup) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label>Group Name</label>
                            <input type="text" name="name" class="form-control" required value="{{ $smallGroup->name }}">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ $smallGroup->description }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Group Leader</label>
                            <select name="leader_id" class="form-control" required>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" {{ $smallGroup->leader_id == $member->id ? 'selected' : '' }}>
                                        {{ $member->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Meeting Day</label>
                                    <select name="meeting_day" class="form-control">
                                        <option value="">Select Day</option>
                                        @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                            <option value="{{ $day }}" {{ $smallGroup->meeting_day == $day ? 'selected' : '' }}>{{ $day }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Meeting Time</label>
                                    <input type="time" name="meeting_time" class="form-control" value="{{ $smallGroup->meeting_time }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Location</label>
                            <input type="text" name="location" class="form-control" value="{{ $smallGroup->location }}">
                        </div>
                        <div class="form-group">
                            <label>Max Members</label>
                            <input type="number" name="max_members" class="form-control" value="{{ $smallGroup->max_members }}" min="5" max="50">
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="active" {{ $smallGroup->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $smallGroup->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update Group</button>
                        <a href="{{ route('small-groups.show', $smallGroup) }}" class="btn btn-default float-right">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
