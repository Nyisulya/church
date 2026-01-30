@extends('layouts.admin')

@section('title', 'My Prayer Requests')

@section('content')
<div class="container-fluid">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">My Prayer Requests</h1>
            <p class="text-muted small mb-0">Track your requests and answers</p>
        </div>
        <a href="{{ route('prayer-requests.wall') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-2"></i> Back to Wall
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Request</th>
                            <th>Status</th>
                            <th>Prayers</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prayers as $prayer)
                        <tr>
                            <td>{{ $prayer->created_at->format('M d, Y') }}</td>
                            <td style="max-width: 400px;">
                                {{ Str::limit($prayer->request, 100) }}
                                @if($prayer->is_private)
                                    <span class="badge badge-secondary ml-2"><i class="fas fa-lock"></i> Private</span>
                                @endif
                            </td>
                            <td>
                                @if($prayer->status === 'active')
                                    <span class="badge badge-primary">Active</span>
                                @elseif($prayer->status === 'answered')
                                    <span class="badge badge-success">Answered</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($prayer->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $prayer->prayer_count }}</td>
                            <td>
                                @if($prayer->status === 'active')
                                <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#answerModal{{ $prayer->id }}">
                                    <i class="fas fa-check mr-1"></i> Mark Answered
                                </button>
                                
                                <!-- Answer Modal -->
                                <div class="modal fade" id="answerModal{{ $prayer->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('prayer-requests.mark-answered', $prayer) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Mark Prayer as Answered</h5>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Request:</strong> {{ $prayer->request }}</p>
                                                    <div class="form-group">
                                                        <label>How did God answer?</label>
                                                        <textarea name="answer" class="form-control" rows="4" required placeholder="Share your testimony..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-success">Save Testimony</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">You haven't submitted any requests yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
