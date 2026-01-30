@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">{{ $user->name }}</h1>
        <div class="flex space-x-2">
            @can('update', $user)
            <a href="{{ route('users.edit', $user) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Edit</a>
            @endcan
            <a href="{{ route('users.index') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Back</a>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Name</label>
                <p class="text-gray-900 font-medium">{{ $user->name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                <p class="text-gray-900">{{ $user->email }}</p>
                @if($user->email_verified_at)
                <span class="text-xs text-green-600">✓ Verified {{ $user->email_verified_at->format('M d, Y') }}</span>
                @else
                <span class="text-xs text-gray-400">Not verified</span>
                @endif
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Role</label>
                @foreach($user->roles as $role)
                <span class="inline-block px-3 py-1 text-sm rounded font-medium
                    @if($role->name == 'super_admin') bg-red-100 text-red-800
                    @elseif($role->name == 'admin') bg-purple-100 text-purple-800
                    @elseif($role->name == 'pastor') bg-blue-100 text-blue-800
                    @elseif($role->name == 'treasurer') bg-green-100 text-green-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                </span>
                @endforeach
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Member Since</label>
                <p class="text-gray-900">{{ $user->created_at->format('M d, Y') }}</p>
                <span class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
