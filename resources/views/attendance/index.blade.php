@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3"><i class="fas fa-calendar-check"></i> {{ __('Attendance Records') }}</h1>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('attendance.scanner') }}" class="btn btn-primary">
                <i class="fas fa-qrcode"></i> {{ __('QR Scanner') }}
            </a>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Recent Events') }}</h3>
        </div>
        <div class="card-body">
            @if($events->count())
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Event Name') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Time') }}</th>
                                <th>{{ __('Attendance') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                            <tr>
                                <td>
                                    <strong>{{ $event->name }}</strong>
                                    @if($event->date->isToday())
                                        <span class="badge badge-success">{{ __('Today') }}</span>
                                    @elseif($event->date->isTomorrow())
                                        <span class="badge badge-info">{{ __('Tomorrow') }}</span>
                                    @endif
                                </td>
                                <td>{{ $event->date->format('M d, Y') }}</td>
                                <td>{{ $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('h:i A') : 'N/A' }}</td>
                                <td>
                                    @php
                                        $total = $event->attendances->count();
                                        $present = $event->attendances->where('status', 'present')->count();
                                    @endphp
                                    <span class="badge badge-primary">{{ $present }} {{ __('Present') }}</span>
                                    <span class="badge badge-secondary">{{ $total }} {{ __('Total') }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('attendance.show', $event) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> {{ __('View/Edit') }}
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <p class="text-muted">{{ __('No events found in the last 30 days.') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
