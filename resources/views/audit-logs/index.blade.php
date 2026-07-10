@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">{{ __('Historia ya Matendo (Audit Logs)') }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('Audit Logs') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">{{ __('Kumbukumbu za Matendo ya Mfumo') }}</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>{{ __('Muda (Date & Time)') }}</th>
                                <th>{{ __('Mtumiaji (User)') }}</th>
                                <th>{{ __('Kitendo (Action)') }}</th>
                                <th>{{ __('Kilichobadilika (Target)') }}</th>
                                <th>{{ __('Mabadiliko (Changes / Details)') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>
                                        <span class="text-nowrap">{{ $log->created_at->format('d/m/Y H:i:s') }}</span>
                                        <br>
                                        <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        @if($log->causer)
                                            <strong>{{ $log->causer->name }}</strong><br>
                                            <small class="badge badge-secondary">{{ $log->causer->roles->pluck('name')->implode(', ') ?: 'No Role' }}</small>
                                        @else
                                            <span class="text-muted"><i>System</i></span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = 'info';
                                            if ($log->description === 'created') $badgeClass = 'success';
                                            if ($log->description === 'deleted') $badgeClass = 'danger';
                                            if ($log->description === 'updated') $badgeClass = 'warning';
                                        @endphp
                                        <span class="badge badge-{{ $badgeClass }} text-uppercase">{{ $log->description }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ class_basename($log->subject_type) }}</strong>
                                        <span class="text-muted">(ID: {{ $log->subject_id }})</span>
                                    </td>
                                    <td>
                                        @if(isset($log->properties['old']) || isset($log->properties['attributes']))
                                            <div class="accordion" id="accordionLog{{ $log->id }}">
                                                <div class="card mb-0 shadow-none border-0">
                                                    <div class="card-header p-0 bg-transparent border-0" id="headingLog{{ $log->id }}">
                                                        <button class="btn btn-link btn-sm text-left p-0" type="button" data-toggle="collapse" data-target="#collapseLog{{ $log->id }}" aria-expanded="false" aria-controls="collapseLog{{ $log->id }}">
                                                            <i class="fas fa-eye mr-1"></i> {{ __('Bonyeza kuona mabadiliko') }}
                                                        </button>
                                                    </div>

                                                    <div id="collapseLog{{ $log->id }}" class="collapse" aria-labelledby="headingLog{{ $log->id }}" data-parent="#accordionLog{{ $log->id }}">
                                                        <div class="card-body p-2 mt-2 bg-light rounded text-xs" style="font-size: 0.8rem; line-height: 1.4;">
                                                            @if(isset($log->properties['attributes']))
                                                                <div class="mb-2">
                                                                    <span class="text-success font-weight-bold">✓ {{ __('Thamani Mpya / Sasa') }}:</span>
                                                                    <ul class="mb-0 pl-3">
                                                                        @foreach($log->properties['attributes'] as $key => $val)
                                                                            @if($key !== 'updated_at' && $key !== 'created_at')
                                                                                <li><strong>{{ $key }}:</strong> <code>{{ is_array($val) ? json_encode($val) : $val }}</code></li>
                                                                            @endif
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            @endif
                                                            @if(isset($log->properties['old']))
                                                                <div>
                                                                    <span class="text-danger font-weight-bold">✗ {{ __('Thamani ya Zamani') }}:</span>
                                                                    <ul class="mb-0 pl-3">
                                                                        @foreach($log->properties['old'] as $key => $val)
                                                                            @if($key !== 'updated_at' && $key !== 'created_at')
                                                                                <li><strong>{{ $key }}:</strong> <code>{{ is_array($val) ? json_encode($val) : $val }}</code></li>
                                                                            @endif
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <span class="text-muted">{{ __('Hakuna kumbukumbu za matendo zilizopatikana.') }}</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 d-flex justify-content-center">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
