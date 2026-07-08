@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-md-8">
            <h1 class="m-0 text-dark">📢 {{ __('Church Announcements (Matangazo)') }}</h1>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('announcements.current') }}" class="btn btn-info mr-2" target="_blank">
                <i class="fas fa-tv"></i> {{ __('Display View') }}
            </a>
            <a href="{{ route('announcements.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('Add Announcement') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('All Announcements') }}</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Content Preview') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Priority') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($announcements as $announcement)
                    <tr>
                        <td>{{ $announcement->announcement_date ? $announcement->announcement_date->format('M d, Y') : '' }}</td>
                        <td><strong>{{ $announcement->title }}</strong></td>
                        <td>{{ Str::limit($announcement->body, 50) }}</td>
                        <td>
                            @if($announcement->is_active)
                                <span class="badge badge-success">{{ __('Active') }}</span>
                            @else
                                <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-info">{{ $announcement->priority }}</span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('announcements.edit', $announcement) }}" class="btn btn-sm btn-warning" title="{{ __('Edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="{{ __('Delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center p-5">
                            <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i><br>
                            {{ __('No announcements yet. Click "Add Announcement" to create one.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $announcements->links() }}
        </div>
    </div>
</div>
@endsection
