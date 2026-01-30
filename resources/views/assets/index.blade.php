@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">📦 Asset & Inventory Manager</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Assets List</h3>
                    <div class="card-tools">
                        <a href="{{ route('assets.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Asset
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Condition</th>
                                <th>Value</th>
                                <th>Purchase Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assets as $asset)
                            <tr>
                                <td>
                                    <strong>{{ $asset->name }}</strong><br>
                                    <small class="text-muted">{{ $asset->serial_number }}</small>
                                </td>
                                <td>{{ $asset->department->name ?? 'Unassigned' }}</td>
                                <td>
                                    @php
                                        $colors = [
                                            'new' => 'success',
                                            'good' => 'primary',
                                            'fair' => 'info',
                                            'poor' => 'warning',
                                            'broken' => 'danger'
                                        ];
                                    @endphp
                                    <span class="badge badge-{{ $colors[$asset->condition] }}">{{ ucfirst($asset->condition) }}</span>
                                </td>
                                <td>{{ $asset->value ? number_format($asset->value, 2) : '-' }}</td>
                                <td>{{ $asset->purchase_date ? $asset->purchase_date->format('M d, Y') : '-' }}</td>
                                <td>
                                    <a href="{{ route('assets.edit', $asset) }}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('assets.destroy', $asset) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No assets found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    {{ $assets->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
