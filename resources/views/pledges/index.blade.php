@extends('layouts.admin')

@push('styles')
<style>
    .custom-pills .nav-link {
        background-color: #ffffff !important;
        color: #495057 !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 12px !important;
        font-size: 1.1rem !important;
        font-weight: 700 !important;
        padding: 15px 20px !important;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05) !important;
        transition: all 0.3s ease !important;
    }
    .custom-pills .nav-link.active {
        background: linear-gradient(135deg, #007bff, #0056b3) !important;
        color: #ffffff !important;
        border-color: #0056b3 !important;
        box-shadow: 0 6px 12px rgba(0,123,255,0.2) !important;
    }
    .custom-pills .nav-link:hover:not(.active) {
        background-color: #f8f9fa !important;
        color: #007bff !important;
        border-color: #007bff !important;
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-3">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <!-- Summary Cards (Visible to Admin/Pastor/Treasurer) -->
    @if(auth()->user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'treasurer']))
    <div class="row mt-3">
        <div class="col-lg-4 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($totalPledged) }}</h3>
                    <p>{{ __('Total Pledged') }} (TZS)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($totalPaid) }}</h3>
                    <p>{{ __('Total Paid') }} (TZS)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $completionRate }}%</h3>
                    <p>{{ __('Completion Rate') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Navigation Card with Tabs -->
    <div class="card card-primary card-outline mt-3">
        <div class="card-header border-bottom-0 bg-light">
            <ul class="nav nav-pills nav-fill custom-pills" id="pledges-tabs" role="tablist" style="gap: 15px; padding: 5px;">
                <li class="nav-item">
                    <a class="nav-link active" id="my-pledges-tab" data-toggle="pill" href="#my-pledges-content" role="tab" aria-controls="my-pledges-content" aria-selected="true">
                        <i class="fas fa-hand-holding-usd mr-2"></i> {{ __('Ahadi Zangu') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="projects-tab" data-toggle="pill" href="#projects-content" role="tab" aria-controls="projects-content" aria-selected="false">
                        <i class="fas fa-project-diagram mr-2"></i> {{ __('Miradi ya Kanisa') }}
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="card-body">
            <div class="tab-content" id="pledges-tabs-content">
                
                <!-- TAB 1: AHADI ZANGU (My Pledges) -->
                <div class="tab-pane fade show active" id="my-pledges-content" role="tabpanel" aria-labelledby="my-pledges-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="text-muted"><i class="fas fa-info-circle mr-2"></i>Orodha ya ahadi zako binafsi kwa ajili ya miradi ya kanisa.</h5>
                        @can('create', App\Models\Pledge::class)
                            <a href="{{ route('pledges.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus mr-1"></i> {{ __('Sajili Ahadi Mpya') }}
                            </a>
                        @endcan
                    </div>

                    @if($pledges->count())
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        @if(auth()->user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'treasurer']))
                                            <th>{{ __('Muumini') }}</th>
                                        @endif
                                        <th>{{ __('Madhumuni / Mradi') }}</th>
                                        <th>{{ __('Kiasi cha Ahadi') }}</th>
                                        <th>{{ __('Kiasi Kilicholipwa') }}</th>
                                        <th>{{ __('Kiasi Kilichobaki') }}</th>
                                        <th>{{ __('Hatua') }}</th>
                                        <th>{{ __('Muda') }}</th>
                                        <th>{{ __('Hali') }}</th>
                                        <th>{{ __('Vitendo') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pledges as $pledge)
                                    <tr>
                                        @if(auth()->user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'treasurer']))
                                            <td>{{ $pledge->member->full_name }}</td>
                                        @endif
                                        <td><strong>{{ $pledge->purpose }}</strong></td>
                                        <td>{{ number_format($pledge->amount) }} TZS</td>
                                        <td class="text-success"><strong>{{ number_format($pledge->amount_paid) }} TZS</strong></td>
                                        <td class="text-danger">{{ number_format($pledge->remaining_balance) }} TZS</td>
                                        <td>
                                            <div class="progress" style="height: 18px;">
                                                <div class="progress-bar bg-{{ $pledge->completion_percentage >= 100 ? 'success' : ($pledge->completion_percentage >= 50 ? 'warning' : 'danger') }}" 
                                                     role="progressbar" 
                                                     style="width: {{ min($pledge->completion_percentage, 100) }}%"
                                                     aria-valuenow="{{ $pledge->completion_percentage }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                    {{ number_format($pledge->completion_percentage, 0) }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td class="small text-muted">
                                            {{ $pledge->start_date->format('M Y') }} - {{ $pledge->end_date->format('M Y') }}
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $pledge->status === 'completed' ? 'success' : 'primary' }}">
                                                {{ ucfirst(__($pledge->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('pledges.show', $pledge) }}" class="btn btn-xs btn-info">
                                                <i class="fas fa-eye mr-1"></i> {{ __('Angalia') }}
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $pledges->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-hand-holding-usd fa-3x text-muted mb-3"></i>
                            <p class="text-muted">{{ __('Hauna ahadi yoyote iliyosajiliwa kwa sasa.') }}</p>
                            @if(!auth()->user()->member)
                                <p class="text-muted">{{ __('Tafadhali kamilisha wasifu wako wa muumini ili kuweza kutoa ahadi.') }}</p>
                                <a href="{{ route('profile.edit') }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-user-plus mr-1"></i> {{ __('Kamilisha Wasifu Wako') }}
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
                
                <!-- TAB 2: MIRADI YA KANISA (Church Projects) -->
                <div class="tab-pane fade" id="projects-content" role="tabpanel" aria-labelledby="projects-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="text-muted"><i class="fas fa-project-diagram mr-2"></i>Miradi inayoendelea kanisani ambayo unaweza kuchangia au kuahidi.</h5>
                        @can('create', App\Models\Project::class)
                            <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus mr-1"></i> {{ __('Unda Mradi Mpya') }}
                            </a>
                        @endcan
                    </div>

                    <div class="row">
                        @forelse($projects as $project)
                            <div class="col-md-6 col-lg-4">
                                <div class="card card-outline card-{{ $project->status === 'active' ? 'success' : ($project->status === 'completed' ? 'secondary' : 'warning') }}">
                                    <div class="card-header">
                                        <h5 class="card-title text-bold">{{ $project->name }}</h5>
                                        <div class="card-tools">
                                            @if(Auth::user()->last_viewed_projects_at && $project->created_at > Auth::user()->last_viewed_projects_at)
                                                <span class="badge badge-success mr-1">
                                                    <i class="fas fa-star"></i> MPYA
                                                </span>
                                            @endif
                                            <span class="badge badge-{{ $project->status === 'active' ? 'success' : ($project->status === 'completed' ? 'secondary' : 'warning') }}">
                                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text text-muted" style="min-height: 50px;">
                                            {{ Str::limit($project->description, 120) }}
                                        </p>
                                        <ul class="list-group list-group-unbordered mb-3">
                                            @if($project->goal_amount)
                                            <li class="list-group-item">
                                                <b>Malengo ya Kiasi (Goal)</b> <span class="float-right text-bold">{{ number_format($project->goal_amount) }} TZS</span>
                                            </li>
                                            @endif
                                            <li class="list-group-item">
                                                <b>Muda wa Mradi</b> 
                                                <span class="float-right text-muted">
                                                    {{ $project->start_date ? $project->start_date->format('M Y') : 'N/A' }} - 
                                                    {{ $project->end_date ? $project->end_date->format('M Y') : 'Hadi ukamilike' }}
                                                </span>
                                            </li>
                                        </ul>
                                        
                                        <div class="btn-group w-100 mb-2">
                                            <a href="{{ route('projects.show', $project) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye mr-1"></i> Angalia
                                            </a>
                                            @can('update', $project)
                                                <a href="{{ route('projects.edit', $project) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit mr-1"></i> Hariri
                                                </a>
                                            @endcan
                                        </div>
                                        
                                        @if($project->status === 'active')
                                            <a href="{{ route('pledges.create', ['project' => $project->name]) }}" class="btn btn-success btn-sm btn-block">
                                                <i class="fas fa-hand-holding-heart mr-1"></i> Weka Ahadi Hapa
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <i class="fas fa-project-diagram fa-3x text-muted mb-3"></i>
                                <p class="text-muted">{{ __('Hakuna miradi ya kanisa iliyotangazwa kwa sasa.') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                


            </div>
        </div>
    </div>
</div>
@endsection
