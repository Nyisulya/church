@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mt-4">
        <div class="col-md-10">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-project-diagram"></i> {{ $project->name }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('projects.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        @can('update', $project)
                            <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5>Description</h5>
                            <p class="text-muted">{{ $project->description ?: 'No description provided.' }}</p>
                            
                            <div class="row mt-4">
                                <div class="col-md-4">
                                    <strong>Status:</strong>
                                    <span class="badge badge-{{ $project->status === 'active' ? 'success' : ($project->status === 'completed' ? 'secondary' : 'warning') }}">
                                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                    </span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Goal Amount:</strong>
                                    <p>{{ $project->goal_amount ? number_format($project->goal_amount, 2) : 'No limit' }}</p>
                                </div>
                                <div class="col-md-4">
                                    <strong>Period:</strong>
                                    <p>
                                        {{ $project->start_date ? $project->start_date->format('M Y') : 'N/A' }} - 
                                        {{ $project->end_date ? $project->end_date->format('M Y') : 'Ongoing' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title w-100 mb-3">Support this Project</h5>
                                    <p class="card-text">Make a pledge to support {{ $project->name }}.</p>
                                    @if($project->status === 'active')
                                        <a href="{{ route('pledges.create', ['project' => $project->name]) }}" class="btn btn-success btn-block">
                                            <i class="fas fa-hand-holding-heart"></i> Pledge Now
                                        </a>
                                    @else
                                        <button class="btn btn-secondary btn-block" disabled>Project Closed</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
