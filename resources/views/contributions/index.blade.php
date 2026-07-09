@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Summary Cards -->
    <div class="row mt-3">
        <!-- Card 1: Total Contributions -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($totals['total']) }}</h3>
                    <p>{{ __('Total Contributions') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
            </div>
        </div>
        <!-- Card 2: Zaka -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($totals['zaka']) }}</h3>
                    <p>{{ __('Total Zaka (Tithe)') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-percentage"></i>
                </div>
            </div>
        </div>
        <!-- Card 3: Sadaka -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($totals['sadaka']) }}</h3>
                    <p>{{ __('Total Sadaka') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-gift"></i>
                </div>
            </div>
        </div>
        <!-- Card 4: Building Fund -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format($totals['building']) }}</h3>
                    <p>{{ __('Building Fund') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-hammer"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Card 5: Projects -->
        <div class="col-lg-4 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ number_format($totals['project']) }}</h3>
                    <p>{{ __('Projects') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-building"></i>
                </div>
            </div>
        </div>
        <!-- Card 6: Thanksgiving -->
        <div class="col-lg-4 col-6">
            <div class="small-box bg-teal">
                <div class="inner">
                    <h3>{{ number_format($totals['thanksgiving']) }}</h3>
                    <p>{{ __('Thanksgiving') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-praying-hands"></i>
                </div>
            </div>
        </div>
        <!-- Card 7: Other -->
        <div class="col-lg-4 col-12">
            <div class="small-box bg-dark">
                <div class="inner">
                    <h3>{{ number_format($totals['other']) }}</h3>
                    <p>{{ __('Other') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-folder-open"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i> {{ __('Contributions History') }}
            </h3>
            <div class="card-tools">
                @can('create', App\Models\Contribution::class)
                    <a href="{{ route('contributions.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> {{ __('Record Contribution') }}
                    </a>
                @endcan
            </div>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <form action="{{ route('contributions.index') }}" method="GET" class="mb-4">
                <div class="row">
                    @if(auth()->user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'financial_officer']))
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('Member') }}</label>
                                <input type="text" name="member_id" class="form-control" placeholder="{{ __('Member ID') }}" value="{{ request('member_id') }}">
                            </div>
                        </div>
                    @endif
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __('Type') }}</label>
                            <select name="type" class="form-control">
                                <option value="">{{ __('All Types') }}</option>
                                <option value="zaka" {{ request('type') == 'zaka' ? 'selected' : '' }}>{{ __('Zaka (Tithe)') }}</option>
                                <option value="sadaka" {{ request('type') == 'sadaka' ? 'selected' : '' }}>{{ __('Sadaka (Offering)') }}</option>
                                <option value="project" {{ request('type') == 'project' ? 'selected' : '' }}>{{ __('Project') }}</option>
                                <option value="building" {{ request('type') == 'building' ? 'selected' : '' }}>{{ __('Building Fund') }}</option>
                                <option value="thanksgiving" {{ request('type') == 'thanksgiving' ? 'selected' : '' }}>{{ __('Thanksgiving') }}</option>
                                <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>{{ __('Date From') }}</label>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>{{ __('Date To') }}</label>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> {{ __('Filter') }}
                                </button>
                                <a href="{{ route('contributions.index') }}" class="btn btn-default">
                                    <i class="fas fa-times"></i> {{ __('Reset') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('Date') }}</th>
                            @if(auth()->user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'financial_officer']))
                                <th>{{ __('Member') }}</th>
                            @endif
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Method') }}</th>
                            <th>{{ __('Reference') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contributions as $contribution)
                            <tr>
                                <td>{{ $contribution->date->format('M d, Y') }}</td>
                                @if(auth()->user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'financial_officer']))
                                    <td>
                                        <a href="{{ route('members.show', $contribution->member) }}">
                                            {{ $contribution->member->full_name }}
                                        </a>
                                    </td>
                                @endif
                                <td>
                                    <span class="badge badge-{{ $contribution->type === 'zaka' ? 'success' : ($contribution->type === 'sadaka' ? 'info' : 'secondary') }}">
                                        {{ ucfirst($contribution->type) }}
                                    </span>
                                </td>
                                <td><strong>{{ number_format($contribution->amount, 2) }}</strong></td>
                                <td>{{ ucfirst($contribution->payment_method) }}</td>
                                <td>{{ $contribution->reference_number ?? '-' }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('contributions.show', $contribution) }}" class="btn btn-xs btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('update', $contribution)
                                            <a href="{{ route('contributions.edit', $contribution) }}" class="btn btn-xs btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $contribution)
                                            <form action="{{ route('contributions.destroy', $contribution) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this contribution? This will also remove the associated financial transaction.') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">{{ __('No contributions found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $contributions->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
