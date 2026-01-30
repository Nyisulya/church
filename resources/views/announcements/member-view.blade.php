@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">📢 {{ __('Church Announcements') }} (Matangazo)</h1>
            <p class="text-muted">{{ now()->format('l, F j, Y') }}</p>
        </div>
    </div>

    @forelse($announcements as $announcement)
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title mb-0">
                    <i class="fas fa-bullhorn mr-2"></i>{{ $announcement->title }}
                </h3>
            </div>
            <div class="card-body">
                <div class="announcement-content" style="font-size: 1.1rem; line-height: 1.8; white-space: pre-line;">
                    {{ $announcement->body }}
                </div>
            </div>
            <div class="card-footer text-muted">
                <i class="far fa-calendar-alt mr-1"></i> 
                {{ $announcement->announcement_date->format('F j, Y') }}
                @if($announcement->author)
                    <span class="ml-3">
                        <i class="far fa-user mr-1"></i> {{ $announcement->author->name }}
                    </span>
                @endif
            </div>
        </div>
    @empty
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-bullhorn fa-4x text-muted mb-4"></i>
                <h3 class="text-muted">{{ __('No announcements available') }}</h3>
                <p class="text-muted">{{ __('Hakuna matangazo kwa sasa. Rudi baadaye!') }}</p>
            </div>
        </div>
    @endforelse
</div>
@endsection
