@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">Create New Small Group</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Group Details</h3>
                </div>
                <form action="{{ route('small-groups.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label>Group Name</label>
                            <input type="text" name="name" class="form-control" required placeholder="e.g. Young Adults Group">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Group Leader</label>
                            <select name="leader_id" class="form-control" required>
                                <option value="">Select Leader</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Meeting Day</label>
                                    <select name="meeting_day" class="form-control">
                                        <option value="">Select Day</option>
                                        <option value="Monday">Monday</option>
                                        <option value="Tuesday">Tuesday</option>
                                        <option value="Wednesday">Wednesday</option>
                                        <option value="Thursday">Thursday</option>
                                        <option value="Friday">Friday</option>
                                        <option value="Saturday">Saturday</option>
                                        <option value="Sunday">Sunday</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Meeting Time</label>
                                    <input type="time" name="meeting_time" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Location</label>
                            <input type="text" name="location" class="form-control" placeholder="e.g. Main Hall">
                        </div>
                        <div class="form-group">
                            <label>Max Members</label>
                            <input type="number" name="max_members" class="form-control" value="15" min="5" max="50">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Create Group</button>
                        <a href="{{ route('small-groups.index') }}" class="btn btn-default float-right">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
