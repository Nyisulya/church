@extends('layouts.admin')

@section('title', 'Manage Books')

@section('content')
<div class="container-fluid">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Manage Spirit of Prophecy Books</h1>
            <p class="text-muted small mb-0">Upload and manage Ellen G. White books in Swahili</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <!-- Upload Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Upload New Book</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Book Title (Swahili) <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required placeholder="e.g. Hatua kwa Kristo">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Cover Image</label>
                            <input type="file" name="cover_image" class="form-control-file" accept="image/*">
                            <small class="text-muted">Optional. Recommended size: 400x600px</small>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Brief description of the book..."></textarea>
                </div>
                <div class="form-group">
                    <label>PDF File <span class="text-danger">*</span></label>
                    <input type="file" name="file" class="form-control-file" accept=".pdf" required>
                    <small class="text-muted">Max size: 50MB</small>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload mr-2"></i> Upload Book
                </button>
            </form>
        </div>
    </div>

    <!-- Books List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Uploaded Books</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Cover</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Uploaded</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($books as $book)
                        <tr>
                            <td style="width: 80px;">
                                @if($book->cover_image_path)
                                    <img src="{{ Storage::url($book->cover_image_path) }}" alt="" class="img-thumbnail" style="width: 60px;">
                                @else
                                    <div class="bg-secondary text-white text-center" style="width: 60px; height: 80px; line-height: 80px;">
                                        <i class="fas fa-book"></i>
                                    </div>
                                @endif
                            </td>
                            <td>{{ $book->title }}</td>
                            <td>{{ Str::limit($book->description, 100) }}</td>
                            <td>{{ $book->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('books.show', $book->id) }}" class="btn btn-sm btn-info" target="_blank">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('books.destroy', $book->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-book fa-3x mb-3"></i>
                                <p>No books uploaded yet.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $books->links() }}
        </div>
    </div>

</div>
@endsection
