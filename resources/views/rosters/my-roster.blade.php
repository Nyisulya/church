@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">📅 My Roster</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Where I'm Serving</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Event</th>
                                <th>Role</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rosters as $roster)
                            <tr>
                                <td>{{ $roster->event->date->format('l, M d, Y @ H:i') }}</td>
                                <td>{{ $roster->event->name }}</td>
                                <td><span class="badge badge-lg badge-info">{{ $roster->role }}</span></td>
                                <td>
                                    <span class="badge badge-success">Confirmed</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center p-5">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i><br>
                                    You are not scheduled for any upcoming events.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
