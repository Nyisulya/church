@extends('layouts.admin')

@section('title', 'Maktaba ya Roho wa Unabii')

@section('content')
<div class="container-fluid">
    
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-2 text-gray-800">📚 Maktaba ya Roho wa Unabii</h1>
            <p class="text-muted">Vitabu vya Ellen G. White kwa Kiswahili</p>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="card shadow mb-4">
        <div class="card-body py-3">
            <form action="{{ route('books.index') }}" method="GET" class="form-inline">
                <div class="input-group w-100">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search text-gray-500"></i></span>
                    </div>
                    <input type="text" class="form-control bg-light border-0" name="search" placeholder="Tafuta kitabu..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">Tafuta</button>
                        @if(request('search'))
                            <a href="{{ route('books.index') }}" class="btn btn-secondary">Futa</a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Books Grid -->
    <div class="row">
        @forelse($books as $book)
        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
            <div class="card shadow h-100 border-0 hover-shadow">
                <a href="{{ route('books.show', $book->id) }}" class="text-decoration-none">
                    @if($book->cover_image_path)
                        @if(str_starts_with($book->cover_image_path, 'http'))
                            <img src="{{ $book->cover_image_path }}" class="card-img-top" alt="{{ $book->title }}" style="height: 300px; object-fit: cover;">
                        @else
                            <img src="{{ Storage::url($book->cover_image_path) }}" class="card-img-top" alt="{{ $book->title }}" style="height: 300px; object-fit: cover;">
                        @endif
                    @else
                        <div class="bg-gradient-primary text-white d-flex align-items-center justify-content-center" style="height: 300px;">
                            <i class="fas fa-book fa-4x"></i>
                        </div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title font-weight-bold text-gray-800 mb-2">{{ $book->title }}</h5>
                        <p class="card-text text-muted small mb-3">{{ Str::limit($book->description, 80) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge badge-primary">📖 PDF</span>
                            <span class="text-primary font-weight-bold">Soma Sasa →</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="text-gray-400 mb-3">
                <i class="fas fa-book fa-4x"></i>
            </div>
            <h4 class="text-gray-600">Hakuna vitabu vilivyopatikana</h4>
            <p class="text-gray-500">Tafadhali rudi baadaye.</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="row">
        <div class="col-12">
            {{ $books->links() }}
        </div>
    </div>

</div>

<style>
.hover-shadow {
    transition: transform 0.2s, box-shadow 0.2s;
}
.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important;
}
</style>
@endsection
