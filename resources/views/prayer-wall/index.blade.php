@extends('layouts.admin')

@section('title', __('Prayer Wall'))

@section('content')
<div class="container-fluid">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">🙏 {{ __('Prayer Wall') }}</h1>
            <p class="text-muted small mb-0">{{ __('Join us in prayer for one another') }}</p>
        </div>
        <a href="{{ route('prayer-requests.my') }}" class="btn btn-primary">
            <i class="fas fa-user-edit mr-2"></i> {{ __('My Requests') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="row">
        <!-- Submit Request Form -->
        <div class="col-md-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">{{ __('Submit Prayer Request') }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('prayer-requests.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>{{ __('How can we pray for you?') }}</label>
                            <textarea name="request" class="form-control" rows="5" required placeholder="{{ __('Share your prayer request here...') }}"></textarea>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_private" name="is_private" value="1">
                                <label class="custom-control-label" for="is_private">{{ __('Keep Private (Pastors Only)') }}</label>
                            </div>
                            <small class="text-muted">{{ __("Private requests won't appear on the public wall.") }}</small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-paper-plane mr-2"></i> {{ __('Submit Request') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Prayer Wall -->
        <div class="col-md-8">
            @forelse($prayers as $prayer)
            <div class="card shadow mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px;">
                                {{ substr($prayer->member->first_name, 0, 1) }}
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-0">{{ $prayer->member->full_name }}</h6>
                                <small class="text-muted">{{ $prayer->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-outline-primary pray-btn" data-id="{{ $prayer->id }}">
                            <i class="fas fa-praying-hands mr-1"></i> {{ __('Prayed') }} (<span id="count-{{ $prayer->id }}">{{ $prayer->prayer_count }}</span>)
                        </button>
                    </div>
                    
                    <p class="mt-3 mb-2">{{ $prayer->request }}</p>

                    @if($prayer->status === 'answered')
                    <div class="alert alert-success mt-3 mb-0">
                        <h6 class="alert-heading"><i class="fas fa-check-circle mr-2"></i> {{ __('Praise God! Prayer Answered:') }}</h6>
                        <p class="mb-0 small">{{ $prayer->answer }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="fas fa-praying-hands fa-3x text-gray-300 mb-3"></i>
                <h4 class="text-gray-500">{{ __('No active prayer requests') }}</h4>
                <p class="text-gray-400">{{ __('Be the first to share a request.') }}</p>
            </div>
            @endforelse

            {{ $prayers->links() }}
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.pray-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        fetch(`/prayer-requests/${id}/pray`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById(`count-${id}`).innerText = data.count;
            this.classList.add('active');
            this.disabled = true;
        });
    });
});
</script>
@endsection
