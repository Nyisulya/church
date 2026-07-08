@extends('layouts.admin')

@section('title', __('Kanda Attendance Tracking'))
@section('page_title', __('Kanda Attendance Tracking: ') . $group->name)

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">{{ __('Chagua Tukio / Ibada') }}</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('small-groups.attendance') }}" method="GET" class="form-inline">
                    <div class="form-group mr-2">
                        <label for="event_id" class="mr-2">{{ __('Event:') }}</label>
                        <select name="event_id" id="event_id" class="form-control" onchange="this.form.submit()">
                            @if($events->isEmpty())
                                <option value="">{{ __('Hakuna matukio ya hivi karibuni') }}</option>
                            @else
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}" {{ ($selectedEvent && $selectedEvent->id == $event->id) ? 'selected' : '' }}>
                                        {{ $event->date->format('d M Y') }} - {{ $event->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <noscript>
                        <button type="submit" class="btn btn-primary">{{ __('Select') }}</button>
                    </noscript>
                </form>
            </div>
        </div>

        @if($selectedEvent)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Wanakanda (Members)') }}</h3>
                <div class="card-tools">
                    <span class="badge badge-success" id="presentCount">
                        Present: {{ $attendances->where('status', 'present')->count() }}
                    </span>
                    <span class="badge badge-danger" id="absentCount">
                        Absent: {{ $attendances->where('status', 'absent')->count() }}
                    </span>
                    <span class="badge badge-secondary" id="notMarkedCount">
                        Not Marked: {{ $group->members->count() - $attendances->count() }}
                    </span>
                </div>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($group->members as $member)
                            @php
                                $attendance = $attendances->get($member->id);
                                $status = $attendance ? $attendance->status : 'not_marked';
                                
                                $statusBadgeClass = 'secondary';
                                $statusText = 'Not Marked';
                                
                                if ($status === 'present') {
                                    $statusBadgeClass = 'success';
                                    $statusText = 'Present';
                                } elseif ($status === 'absent') {
                                    $statusBadgeClass = 'danger';
                                    $statusText = 'Absent';
                                } elseif ($status === 'late') {
                                    $statusBadgeClass = 'warning';
                                    $statusText = 'Late';
                                }
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $member->full_name }}</strong><br>
                                    <small class="text-muted">{{ $member->member_number }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $statusBadgeClass }} attendance-status-{{ $member->id }}">
                                        {{ __($statusText) }}
                                        @if($attendance && $attendance->scanned_by)
                                            <small><br>({{ $attendance->scanner->name ?? 'Scanner' }})</small>
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-success mark-btn" data-member="{{ $member->id }}" data-status="present" {{ $status === 'present' ? 'disabled' : '' }}>
                                            <i class="fas fa-check"></i> {{ __('Present') }}
                                        </button>
                                        <button type="button" class="btn btn-outline-danger mark-btn" data-member="{{ $member->id }}" data-status="absent" {{ $status === 'absent' ? 'disabled' : '' }}>
                                            <i class="fas fa-times"></i> {{ __('Absent') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">{{ __('Hakuna wanakanda kwenye kanda hii.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> {{ __('Tafadhali chagua ibada/tukio hapo juu ili kuendelea.') }}
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const eventId = '{{ $selectedEvent ? $selectedEvent->id : '' }}';
    
    $('.mark-btn').on('click', function(e) {
        e.preventDefault();
        if (!eventId) return;
        
        const btn = $(this);
        const memberId = btn.data('member');
        const status = btn.data('status');
        
        // Disable all buttons in this row temporarily
        const rowBtns = btn.closest('.btn-group').find('.mark-btn');
        rowBtns.prop('disabled', true);
        
        $.ajax({
            url: '{{ route('small-groups.attendance.mark') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                event_id: eventId,
                member_id: memberId,
                status: status
            },
            success: function(response) {
                if (response.success) {
                    // Update the status badge
                    const badge = $('.attendance-status-' + memberId);
                    
                    let badgeClass = 'secondary';
                    let statusText = 'Not Marked';
                    
                    if (status === 'present') {
                        badgeClass = 'success';
                        statusText = 'Present';
                    } else if (status === 'absent') {
                        badgeClass = 'danger';
                        statusText = 'Absent';
                    } else if (status === 'late') {
                        badgeClass = 'warning';
                        statusText = 'Late';
                    }
                    
                    badge.removeClass('badge-success badge-danger badge-warning badge-secondary')
                         .addClass('badge-' + badgeClass)
                         .html(statusText + '<br><small>(You)</small>');
                    
                    // Enable/disable buttons based on new status
                    rowBtns.each(function() {
                        $(this).prop('disabled', $(this).data('status') === status);
                    });
                    
                    // Show quick toast notification
                    $(document).Toasts('create', {
                        class: 'bg-success',
                        title: 'Imefanikiwa',
                        body: response.message,
                        autohide: true,
                        delay: 2000
                    });
                    
                    // Optional: recalculate totals via page reload if preferred, or calculate client-side
                    // location.reload();
                }
            },
            error: function(xhr) {
                // Re-enable buttons on error
                const currentStatusBadge = $('.attendance-status-' + memberId);
                let currentStatus = 'not_marked';
                if(currentStatusBadge.hasClass('badge-success')) currentStatus = 'present';
                if(currentStatusBadge.hasClass('badge-danger')) currentStatus = 'absent';
                
                rowBtns.each(function() {
                    $(this).prop('disabled', $(this).data('status') === currentStatus);
                });
                
                $(document).Toasts('create', {
                    class: 'bg-danger',
                    title: 'Hitilafu',
                    body: xhr.responseJSON ? xhr.responseJSON.message : 'Kuna tatizo limetokea.',
                    autohide: true,
                    delay: 4000
                });
            }
        });
    });
});
</script>
@endsection
