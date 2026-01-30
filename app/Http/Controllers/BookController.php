<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    /**
     * Display the public library.
     */
    public function index(Request $request)
    {
        $query = Book::query();

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $books = $query->latest()->paginate(12);

        return view('books.index', compact('books'));
    }

    /**
     * Display the admin management page.
     */
    public function adminIndex()
    {
        $books = Book::latest()->paginate(20);
        return view('books.admin', compact('books'));
    }

    /**
     * Store a newly created book.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|mimes:pdf|max:50000', // 50MB max
            'cover_image' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
        ]);

        $book = new Book();
        $book->title = $request->title;
        $book->author = 'Ellen G. White';
        $book->language = 'sw';
        $book->description = $request->description;

        // Upload PDF
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('books', 'public');
            $book->file_path = $path;
        }

        // Upload Cover
        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('books/covers', 'public');
            $book->cover_image_path = $path;
        }

        $book->save();

        return redirect()->route('books.admin')->with('success', 'Book uploaded successfully.');
    }

    /**
     * Display the book reader.
     */
    public function show(Book $book)
    {
        return view('books.read', compact('book'));
    }

    /**
     * Remove the specified book.
     */
    public function destroy(Book $book)
    {
        // Delete files
        if ($book->file_path) {
            Storage::disk('public')->delete($book->file_path);
        }
        if ($book->cover_image_path) {
            Storage::disk('public')->delete($book->cover_image_path);
        }

        $book->delete();

        return redirect()->route('books.admin')->with('success', 'Book deleted successfully.');
    }
}
