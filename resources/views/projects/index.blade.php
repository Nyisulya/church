@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card card-primary card-outline mt-3">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-project-diagram"></i> Church Projects
            </h3>
            <div class="card-tools">
                @can('create', App\Models\Project::class)
                    <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Create Project
                    </a>
                @endcan
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            <div class="row">
                @forelse($projects as $project)
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-outline card-{{ $project->status === 'active' ? 'success' : ($project->status === 'completed' ? 'secondary' : 'warning') }}">
                            <div class="card-header">
                                <h5 class="card-title">{{ $project->name }}</h5>
                                <div class="card-tools">
                                    @if(Auth::user()->last_viewed_projects_at && $project->created_at > Auth::user()->last_viewed_projects_at)
                                        <span class="badge badge-success mr-1">
                                            <i class="fas fa-star"></i> NEW
                                        </span>
                                    @endif
                                    <span class="badge badge-{{ $project->status === 'active' ? 'success' : ($project->status === 'completed' ? 'secondary' : 'warning') }}">
                                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="card-text text-muted">
                                    {{ Str::limit($project->description, 100) }}
                                </p>
                                <ul class="list-group list-group-unbordered mb-3">
                                    @if($project->goal_amount)
                                    <li class="list-group-item">
                                        <b>Goal Amount</b> <span class="float-right">{{ number_format($project->goal_amount, 2) }}</span>
                                    </li>
                                    @endif
                                    <li class="list-group-item">
                                        <b>Period</b> 
                                        <span class="float-right">
                                            {{ $project->start_date ? $project->start_date->format('M Y') : 'N/A' }} - 
                                            {{ $project->end_date ? $project->end_date->format('M Y') : 'Ongoing' }}
                                        </span>
                                    </li>
                                </ul>
                                
                                <div class="btn-group w-100">
                                    <a href="{{ route('projects.show', $project) }}" class="btn btn-info">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @can('update', $project)
                                        <a href="{{ route('projects.edit', $project) }}" class="btn btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    @endcan
                                </div>
                                
                                @if($project->status === 'active')
                                    <a href="{{ route('pledges.create', ['project' => $project->name]) }}" class="btn btn-success btn-block mt-2">
                                        <i class="fas fa-hand-holding-heart"></i> Pledge Now
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-project-diagram fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No projects found.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-3">
                {{ $projects->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
