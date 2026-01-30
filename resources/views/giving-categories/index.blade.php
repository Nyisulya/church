@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">💰 Manage Categories</h1>
            <p class="text-muted">Manage categories for income and expenses</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            {{-- Income Categories --}}
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-arrow-down text-success"></i> Income Categories</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th width="200">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories->where('type', 'income') as $category)
                            <tr>
                                <td><strong>{{ $category->name }}</strong></td>
                                <td>{{ $category->description }}</td>
                                <td>
                                    @if($category->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#editModal{{ $category->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('giving-categories.toggle', $category) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-{{ $category->is_active ? 'warning' : 'success' }}">
                                            <i class="fas fa-{{ $category->is_active ? 'ban' : 'check' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('giving-categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal{{ $category->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Category</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form action="{{ route('giving-categories.update', $category) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Category Name</label>
                                                    <input type="text" name="name" class="form-control" required value="{{ $category->name }}">
                                                </div>
                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <textarea name="description" class="form-control" rows="2">{{ $category->description }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center p-4 text-muted">No income categories yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Expense Categories --}}
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-arrow-up text-danger"></i> Expense Categories</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th width="200">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories->where('type', 'expense') as $category)
                            <tr>
                                <td><strong>{{ $category->name }}</strong></td>
                                <td>{{ $category->description }}</td>
                                <td>
                                    @if($category->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#editModalExp{{ $category->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('giving-categories.toggle', $category) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-{{ $category->is_active ? 'warning' : 'success' }}">
                                            <i class="fas fa-{{ $category->is_active ? 'ban' : 'check' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('giving-categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModalExp{{ $category->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Category</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form action="{{ route('giving-categories.update', $category) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Category Name</label>
                                                    <input type="text" name="name" class="form-control" required value="{{ $category->name }}">
                                                </div>
                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <textarea name="description" class="form-control" rows="2">{{ $category->description }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center p-4 text-muted">No expense categories yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add New Category -->
        <div class="col-md-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Add New Category</h3>
                </div>
                <form action="{{ route('giving-categories.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label>Category Type</label>
                            <select name="type" class="form-control" required>
                                <option value="income">Income Category</option>
                                <option value="expense">Expense Category</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Category Name</label>
                            <input type="text" name="name" class="form-control" required placeholder="e.g. Utilities, Tithes">
                        </div>
                        <div class="form-group">
                            <label>Description (Optional)</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Brief description..."></textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-plus"></i> Add Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
