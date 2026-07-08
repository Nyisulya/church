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
                <form id="attendanceForm">
                    @csrf
                    <input type="hidden" name="event_id" value="{{ $selectedEvent->id }}">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Action (Chagua)') }}</th>
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
                                        <div class="form-group mb-0">
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="present_{{ $member->id }}" name="attendance[{{ $member->id }}]" class="custom-control-input" value="present" {{ $status === 'present' ? 'checked' : '' }}>
                                                <label class="custom-control-label text-success" for="present_{{ $member->id }}">Present</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="absent_{{ $member->id }}" name="attendance[{{ $member->id }}]" class="custom-control-input" value="absent" {{ $status === 'absent' ? 'checked' : '' }}>
                                                <label class="custom-control-label text-danger" for="absent_{{ $member->id }}">Absent</label>
                                            </div>
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
                    @if($group->members->count() > 0)
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary" id="saveBtn">
                            <i class="fas fa-save"></i> {{ __('Save Attendance') }}
                        </button>
                    </div>
                    @endif
                </form>
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
    $('#attendanceForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const btn = $('#saveBtn');
        const originalText = btn.html();
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        // Convert form data to a format we can send multiple easily
        const formData = form.serializeArray();
        const eventId = form.find('input[name="event_id"]').val();
        
        // We will send requests for each member sequentially or we could create a bulk mark route.
        // Let's use the existing bulk-mark route if possible, or send individual requests.
        // Since we have a bulk mark route in AttendanceController, let's use that!
        
        let presentIds = [];
        let absentIds = [];
        
        formData.forEach(item => {
            if (item.name.startsWith('attendance[')) {
                let memberId = item.name.match(/\[(\d+)\]/)[1];
                if (item.value === 'present') {
                    presentIds.push(memberId);
                } else if (item.value === 'absent') {
                    absentIds.push(memberId);
                }
            }
        });
        
        $.ajax({
            url: '{{ route('small-groups.attendance.bulk-mark') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                event_id: eventId,
                present: presentIds,
                absent: absentIds
            },
            success: function(response) {
                btn.prop('disabled', false).html(originalText);
                
                if (response.success) {
                    // Update badges
                    presentIds.forEach(id => {
                        $('.attendance-status-' + id).removeClass('badge-secondary badge-danger badge-warning').addClass('badge-success').html('Present<br><small>(You)</small>');
                    });
                    absentIds.forEach(id => {
                        $('.attendance-status-' + id).removeClass('badge-secondary badge-success badge-warning').addClass('badge-danger').html('Absent<br><small>(You)</small>');
                    });
                    
                    // Update counts
                    if(response.counts) {
                        $('#presentCount').text('Present: ' + response.counts.present);
                        $('#absentCount').text('Absent: ' + response.counts.absent);
                        $('#notMarkedCount').text('Not Marked: ' + response.counts.not_marked);
                    }
                    
                    $(document).Toasts('create', {
                        class: 'bg-success',
                        title: 'Imefanikiwa',
                        body: 'Mahudhurio yamehifadhiwa kikamilifu!',
                        autohide: true,
                        delay: 3000
                    });
                } else {
                    alert("Imefeli lakini server imejibu: " + JSON.stringify(response));
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(originalText);
                
                // Show detailed popup error message to help debug
                alert("Hitilafu imetokea!\nStatus: " + xhr.status + "\nJibu kutoka Server: " + xhr.responseText);
                
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
