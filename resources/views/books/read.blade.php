@extends('layouts.admin')

@section('title', $book->title)

@section('content')
<div class="container-fluid">
    
    @php
        $pdfUrl = str_starts_with($book->file_path, 'http') ? $book->file_path : Storage::url($book->file_path);
    @endphp

    <!-- Book Info Card -->
    <div class="card shadow">
        <div class="card-body text-center py-5">
            <i class="fas fa-book fa-4x text-primary mb-4"></i>
            <h2 class="h3 mb-2">{{ $book->title }}</h2>
            <p class="text-muted mb-4">{{ $book->author }}</p>
            
            @if($book->description)
                <p class="mb-4">{{ $book->description }}</p>
            @endif

            <div class="btn-group mb-3" role="group">
                <a href="{{ $pdfUrl }}" class="btn btn-primary btn-lg" target="_blank">
                    <i class="fas fa-book-open mr-2"></i> Fungua & Soma PDF
                </a>
                <a href="{{ $pdfUrl }}" class="btn btn-success btn-lg" download>
                    <i class="fas fa-download mr-2"></i> Pakua PDF
                </a>
            </div>

            <div class="mt-3">
                <a href="{{ route('books.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i> Rudi kwa Maktaba
                </a>
            </div>

            <div class="alert alert-info mt-4">
                <i class="fas fa-info-circle mr-2"></i>
                Bonyeza "Fungua & Soma PDF" ili kusoma kitabu katika tab mpya. 
                PDF itatoka kwenye tovuti ya EGW Writings.
            </div>
        </div>
    </div>

</div>

<script>
// Auto-open PDF in new tab when page loads
window.addEventListener('load', function() {
    // Uncomment this to auto-open the PDF
    // window.open('{{ $pdfUrl }}', '_blank');
});
</script>
@endsection
