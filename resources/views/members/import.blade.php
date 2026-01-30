@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto bg-white shadow rounded p-6">
    <h1 class="text-2xl font-semibold mb-4">Import Members</h1>
    
    <div class="mb-4 bg-blue-50 border-l-4 border-blue-500 p-4">
        <p class="text-sm text-blue-700">
            <strong>Supported Formats:</strong> CSV, Excel (.xlsx, .xls)<br>
            <strong>Required Headers:</strong> <code>name, email, password</code><br>
            <span class="text-xs text-gray-500">Note: 'confirm_password' is optional for import.</span>
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4">
            <ul class="list-disc list-inside text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('members.import.process') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
            <label class="block font-medium text-gray-700">Upload File</label>
            <input type="file" name="csv_file" class="mt-1 block w-full border border-gray-300 rounded p-2" accept=".csv, .xlsx, .xls" required>
        </div>

        <div class="mt-6">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Import Members</button>
            <a href="{{ route('members.index') }}" class="ml-4 text-gray-600 hover:underline">Cancel</a>
        </div>
    </form>
</div>
@endsection
