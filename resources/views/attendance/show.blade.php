@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3"><i class="fas fa-calendar-check"></i> {{ __('Attendance for') }}: {{ $event->name }}</h1>
            <p class="text-muted">{{ $event->date->format('F d, Y') }} at {{ $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('h:i A') : 'N/A' }}</p>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('attendance.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('Back to Events') }}
            </a>
            <a href="{{ route('attendance.scanner') }}" class="btn btn-primary">
                <i class="fas fa-qrcode"></i> QR Scanner
            </a>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Mark Attendance') }}</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-success" onclick="markAllPresent()">
                    <i class="fas fa-check-double"></i> {{ __('Mark All Present') }}
                </button>
            </div>
        </div>
        <div class="card-body">
            <form id="attendance-form" method="POST" action="{{ route('attendance.bulk-mark', $event) }}">
                @csrf
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>{{ __('Member') }}</th>
                                <th>{{ __('Member Number') }}</th>
                                <th width="15%">{{ __('Status') }}</th>
                                <th width="20%">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($members as $index => $member)
                            @php
                                $attendance = $attendances->get($member->id);
                                $status = $attendance ? $attendance->status : null;
                            @endphp
                            <tr id="member-{{ $member->id }}" class="
                                @if($status === 'present') table-success
                                @elseif($status === 'absent') table-danger
                                @elseif($status === 'late') table-warning
                                @endif
                            ">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $member->full_name }}</strong><br>
                                    <small class="text-muted">{{ $member->email }}</small>
                                </td>
                                <td>{{ $member->member_number ?? 'N/A' }}</td>
                                <td>
                                    @if($status === 'present')
                                        <span class="badge badge-success">{{ __('Present') }}</span>
                                    @elseif($status === 'absent')
                                        <span class="badge badge-danger">{{ __('Absent') }}</span>
                                    @elseif($status === 'late')
                                        <span class="badge badge-warning">{{ __('Late') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ __('Not Marked') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-success" onclick="markStatus({{ $member->id }}, 'present')">
                                            <i class="fas fa-check"></i> {{ __('Present') }}
                                        </button>
                                        <button type="button" class="btn btn-warning" onclick="markStatus({{ $member->id }}, 'late')">
                                            <i class="fas fa-clock"></i> {{ __('Late') }}
                                        </button>
                                        <button type="button" class="btn btn-danger" onclick="markStatus({{ $member->id }}, 'absent')">
                                            <i class="fas fa-times"></i> {{ __('Absent') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <p class="text-muted">
                        <strong>{{ __('Summary') }}:</strong>
                        {{ __('Present') }}: <span class="badge badge-success">{{ $attendances->where('status', 'present')->count() }}</span>,
                        {{ __('Late') }}: <span class="badge badge-warning">{{ $attendances->where('status', 'late')->count() }}</span>,
                        {{ __('Absent') }}: <span class="badge badge-danger">{{ $attendances->where('status', 'absent')->count() }}</span>,
                        {{ __('Not Marked') }}: <span class="badge badge-secondary">{{ $members->count() - $attendances->count() }}</span>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Helper function to update row styling based on status
function updateRowStyle(memberId, status) {
    const row = document.getElementById(`member-${memberId}`);
    if (!row) return;
    
    // Remove all status classes
    row.classList.remove('table-success', 'table-danger', 'table-warning');
    
    // Add appropriate class based on status
    if (status === 'present') {
        row.classList.add('table-success');
    } else if (status === 'absent') {
        row.classList.add('table-danger');
    } else if (status === 'late') {
        row.classList.add('table-warning');
    }
    
    // Update badge in the status column
    const statusCell = row.querySelector('td:nth-child(4)');
    if (statusCell) {
        let badgeClass = 'badge-secondary';
        let badgeText = '{{ __('Not Marked') }}';
        
        if (status === 'present') {
            badgeClass = 'badge-success';
            badgeText = '{{ __('Present') }}';
        } else if (status === 'absent') {
            badgeClass = 'badge-danger';
            badgeText = '{{ __('Absent') }}';
        } else if (status === 'late') {
            badgeClass = 'badge-warning';
            badgeText = '{{ __('Late') }}';
        }
        
        statusCell.innerHTML = `<span class="badge ${badgeClass}">${badgeText}</span>`;
    }
}

// Helper function to update summary counts
function updateSummaryCounts(counts) {
    const summary = document.querySelector('.mt-4 p.text-muted');
    if (summary && counts) {
        summary.innerHTML = `
            <strong>{{ __('Summary') }}:</strong>
            {{ __('Present') }}: <span class="badge badge-success">${counts.present}</span>,
            {{ __('Late') }}: <span class="badge badge-warning">${counts.late}</span>,
            {{ __('Absent') }}: <span class="badge badge-danger">${counts.absent}</span>,
            {{ __('Not Marked') }}: <span class="badge badge-secondary">${counts.not_marked}</span>
        `;
    }
}

// Helper function to show toast notification
function showToast(message, type = 'success') {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    `;
    
    // Insert at the top of container-fluid
    const container = document.querySelector('.container-fluid');
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = alertHtml;
    container.insertBefore(tempDiv.firstElementChild, container.firstChild);
    
    // Auto-dismiss after 3 seconds
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 3000);
}

function markStatus(memberId, status) {
    console.log('Marking attendance for member:', memberId, 'Status:', status);
    
    fetch("{{ route('attendance.mark', $event) }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            member_id: memberId,
            status: status
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        if (data.success) {
            // Update the row styling and badge
            updateRowStyle(memberId, status);
            
            // Update summary counts
            updateSummaryCounts(data.counts);
            
            // Show success notification
            showToast(data.message);
        } else {
            showToast(data.message || '{{ __('Failed to update attendance') }}', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('{{ __('Failed to update attendance') }}', 'danger');
    });
}

function markAllPresent() {
    if (confirm('{{ __('Mark all members as present?') }}')) {
        const memberIds = @json($members->pluck('id'));
        
        fetch("{{ route('attendance.bulk-mark', $event) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                present: memberIds
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update all rows to present
                memberIds.forEach(memberId => {
                    updateRowStyle(memberId, 'present');
                });
                
                // Update summary counts
                updateSummaryCounts(data.counts);
                
                // Show success notification
                showToast(data.message);
            } else {
                showToast(data.message || '{{ __('Failed to mark all present') }}', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('{{ __('Failed to mark all present') }}', 'danger');
        });
    }
}
</script>
@endpush
@endsection
