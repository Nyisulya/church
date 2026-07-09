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

            <!-- Kanda Performance Dashboard -->
            <div class="card mt-4">
                <div class="card-header bg-navy">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line"></i> {{ __('Mchanganuo wa Kanda (Kanda Project Targets)') }}
                    </h3>
                    @can('update', $project)
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-manage-goals">
                                <i class="fas fa-cog"></i> {{ __('Simamia Malengo ya Kanda') }}
                            </button>
                        </div>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead>
                                <tr class="bg-light">
                                    <th>{{ __('Kanda (Small Group)') }}</th>
                                    <th class="text-right">{{ __('Lengo la Kanda (Target)') }}</th>
                                    <th class="text-right">{{ __('Ahadi Zilizowekwa (Pledged)') }}</th>
                                    <th class="text-right">{{ __('Kiasi Kilicholipwa (Paid)') }}</th>
                                    <th class="text-right">{{ __('Bado Kiasi Gani') }}</th>
                                    <th style="width: 25%">{{ __('Utekelezaji %') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($groups as $group)
                                    @php
                                        $remaining = max(0, $group['target_amount'] - $group['total_paid']);
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $group['name'] }}</strong></td>
                                        <td class="text-right font-weight-bold text-primary">
                                            TZS {{ number_format($group['target_amount'], 2) }}
                                        </td>
                                        <td class="text-right">
                                            TZS {{ number_format($group['total_pledged'], 2) }}
                                        </td>
                                        <td class="text-right text-success font-weight-bold">
                                            TZS {{ number_format($group['total_paid'], 2) }}
                                        </td>
                                        <td class="text-right text-danger">
                                            TZS {{ number_format($remaining, 2) }}
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="mr-2 font-weight-bold">{{ $group['percentage'] }}%</span>
                                                <div class="progress progress-sm flex-grow-1" style="height: 10px;">
                                                    <div class="progress-bar bg-{{ $group['percentage'] >= 100 ? 'success' : ($group['percentage'] >= 50 ? 'info' : ($group['percentage'] >= 25 ? 'warning' : 'danger')) }}"
                                                         role="progressbar"
                                                         style="width: {{ $group['percentage'] }}%"
                                                         aria-valuenow="{{ $group['percentage'] }}"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">{{ __('Hakuna kanda zilizosajiliwa.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @can('update', $project)
                <!-- Modal for setting group goals -->
                <div class="modal fade" id="modal-manage-goals" tabindex="-1" role="dialog" aria-labelledby="modalManageGoalsLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form action="{{ route('projects.update-group-goals', $project) }}" method="POST">
                                @csrf
                                <div class="modal-header bg-primary">
                                    <h5 class="modal-title text-white" id="modalManageGoalsLabel">
                                        <i class="fas fa-cog"></i> {{ __('Simamia Malengo ya Kanda') }}
                                    </h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p class="text-muted small">
                                        {{ __('Weka malengo ya kifedha kwa kila kanda kwa ajili ya mradi huu wa') }} <strong>{{ $project->name }}</strong>.
                                    </p>
                                    @foreach($groups as $group)
                                        <div class="form-group row align-items-center">
                                            <label class="col-sm-5 col-form-label font-weight-bold">{{ $group['name'] }}</label>
                                            <div class="col-sm-7">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">TZS</span>
                                                    </div>
                                                    <input type="number" 
                                                           name="targets[{{ $group['id'] }}]" 
                                                           class="form-control" 
                                                           step="0.01" 
                                                           min="0" 
                                                           placeholder="0.00" 
                                                           value="{{ $group['target_amount'] }}">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Ghairi') }}</button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i> {{ __('Hifadhi Malengo') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>
</div>
@endsection
