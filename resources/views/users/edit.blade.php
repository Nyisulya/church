@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Edit User</h1>
        <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-gray-900">← Back</a>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Name *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border-gray-300 rounded @error('name') border-red-500 @enderror" required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Email *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border-gray-300 rounded @error('email') border-red-500 @enderror" required>
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">New Password (leave blank to keep current)</label>
                <input type="password" name="password" class="w-full border-gray-300 rounded @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-sm text-gray-500 mt-1">Minimum 8 characters</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Confirm New Password</label>
                <input type="password" name="password_confirmation" class="w-full border-gray-300 rounded">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Role *</label>
                <select name="role" class="w-full border-gray-300 rounded @error('role') border-red-500 @enderror" required>
                    @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                    </option>
                    @endforeach
                </select>
                @error('role')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex space-x-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Update User
                </button>
                <a href="{{ route('users.index') }}" class="bg-gray-300 text-gray-800 px-6 py-2 rounded hover:bg-gray-400">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
