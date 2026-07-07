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
                    <h3 class="card-title">{{ __('Group Information') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('small-groups.edit', $smallGroup) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-edit"></i> {{ __('Edit') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <p>{{ $smallGroup->description }}</p>
                    <p><strong>{{ __('Leader') }}:</strong> {{ $smallGroup->leader->full_name }}</p>
                    @if($smallGroup->meeting_day)
                    <p><strong>{{ __('Meeting') }}:</strong> {{ __($smallGroup->meeting_day) }} @ {{ $smallGroup->meeting_time }}</p>
                    @endif
                    @if($smallGroup->location)
                    <p><strong>{{ __('Location') }}:</strong> {{ $smallGroup->location }}</p>
                    @endif
                    <p><strong>{{ __('Capacity') }}:</strong> {{ $smallGroup->members->count() }} / {{ $smallGroup->max_members }}</p>
                </div>
            </div>

            <!-- Members -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Members') }} ({{ $smallGroup->members->count() }})</h3>
                    <div class="card-tools">
                        @if(!$smallGroup->isFull())
                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addMemberModal">
                            <i class="fas fa-plus"></i> {{ __('Add Member') }}
                        </button>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Role') }}</th>
                                <th>{{ __('Joined') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($smallGroup->members as $member)
                            <tr>
                                <td>{{ $member->full_name }}</td>
                                <td>
                                    @if($member->pivot->role === 'co-leader')
                                    <span class="badge badge-info">{{ __('Co-Leader') }}</span>
                                    @else
                                    <span class="badge badge-secondary">{{ __('Member') }}</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($member->pivot->joined_at)->format('M d, Y') }}</td>
                                <td>
                                    <form action="{{ route('small-groups.remove-member', [$smallGroup, $member]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Remove this member?') }}')">
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
                    <h3 class="card-title">{{ __('Log Meeting') }}</h3>
                </div>
                <form action="{{ route('small-groups.store-meeting', $smallGroup) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label>{{ __('Date') }}</label>
                            <input type="date" name="meeting_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Topic') }}</label>
                            <input type="text" name="topic" class="form-control" placeholder="{{ __('Optional') }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __('Attendees') }}</label>
                            <input type="number" name="attendees_count" class="form-control" required min="0" max="{{ $smallGroup->members->count() }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __('Notes') }}</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-block">{{ __('Save Meeting') }}</button>
                    </div>
                </form>
            </div>

            <!-- Recent Meetings -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Recent Meetings') }}</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm">
                        <tbody>
                            @forelse($smallGroup->meetings->sortByDesc('meeting_date')->take(10) as $meeting)
                            <tr>
                                <td>
                                    <strong>{{ $meeting->meeting_date->format('M d') }}</strong><br>
                                    <small>{{ $meeting->topic ?: __('No topic') }}</small><br>
                                    <small class="text-muted">{{ $meeting->attendees_count }} {{ __('attended') }}</small>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-center p-3">{{ __('No meetings logged yet') }}</td>
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
                <h5 class="modal-title">{{ __('Add Member to Group') }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('small-groups.add-member', $smallGroup) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ __('Select Member') }}</label>
                        <select name="member_id" class="form-control" required>
                            <option value="">{{ __('Choose...') }}</option>
                            @foreach($availableMembers as $member)
                                <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ __('Role') }}</label>
                        <select name="role" class="form-control">
                            <option value="member">{{ __('Member') }}</option>
                            <option value="co-leader">{{ __('Co-Leader') }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Add Member') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
