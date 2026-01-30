@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">{{ $smallGroup->name }}</h1>
        </div>
    </div>

    <div class="row">
        <!-- Group Info -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Group Information</h3>
                    <div class="card-tools">
                        <a href="{{ route('small-groups.edit', $smallGroup) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <p>{{ $smallGroup->description }}</p>
                    <p><strong>Leader:</strong> {{ $smallGroup->leader->full_name }}</p>
                    @if($smallGroup->meeting_day)
                    <p><strong>Meeting:</strong> {{ $smallGroup->meeting_day }} @ {{ $smallGroup->meeting_time }}</p>
                    @endif
                    @if($smallGroup->location)
                    <p><strong>Location:</strong> {{ $smallGroup->location }}</p>
                    @endif
                    <p><strong>Capacity:</strong> {{ $smallGroup->members->count() }} / {{ $smallGroup->max_members }}</p>
                </div>
            </div>

            <!-- Members -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Members ({{ $smallGroup->members->count() }})</h3>
                    <div class="card-tools">
                        @if(!$smallGroup->isFull())
                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addMemberModal">
                            <i class="fas fa-plus"></i> Add Member
                        </button>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Joined</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($smallGroup->members as $member)
                            <tr>
                                <td>{{ $member->full_name }}</td>
                                <td>
                                    @if($member->pivot->role === 'co-leader')
                                    <span class="badge badge-info">Co-Leader</span>
                                    @else
                                    <span class="badge badge-secondary">Member</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($member->pivot->joined_at)->format('M d, Y') }}</td>
                                <td>
                                    <form action="{{ route('small-groups.remove-member', [$smallGroup, $member]) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this member?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Log Meeting -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Log Meeting</h3>
                </div>
                <form action="{{ route('small-groups.store-meeting', $smallGroup) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="meeting_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Topic</label>
                            <input type="text" name="topic" class="form-control" placeholder="Optional">
                        </div>
                        <div class="form-group">
                            <label>Attendees</label>
                            <input type="number" name="attendees_count" class="form-control" required min="0" max="{{ $smallGroup->members->count() }}">
                        </div>
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-block">Save Meeting</button>
                    </div>
                </form>
            </div>

            <!-- Recent Meetings -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Recent Meetings</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm">
                        <tbody>
                            @forelse($smallGroup->meetings->sortByDesc('meeting_date')->take(10) as $meeting)
                            <tr>
                                <td>
                                    <strong>{{ $meeting->meeting_date->format('M d') }}</strong><br>
                                    <small>{{ $meeting->topic ?: 'No topic' }}</small><br>
                                    <small class="text-muted">{{ $meeting->attendees_count }} attended</small>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-center p-3">No meetings logged yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Member to Group</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('small-groups.add-member', $smallGroup) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Select Member</label>
                        <select name="member_id" class="form-control" required>
                            <option value="">Choose...</option>
                            @foreach($availableMembers as $member)
                                <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" class="form-control">
                            <option value="member">Member</option>
                            <option value="co-leader">Co-Leader</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Member</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
