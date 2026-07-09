@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Users Management</h1>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('members.import') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                <i class="fas fa-file-excel mr-1"></i> Import Users
            </a>
            <a href="{{ route('users.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-plus mr-1"></i> Create New User
            </a>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white shadow rounded p-6">
            <div class="text-sm text-gray-600 mb-2">Total Users</div>
            <div class="text-3xl font-bold text-blue-600">{{ $users->total() }}</div>
        </div>
        <div class="bg-white shadow rounded p-6">
            <div class="text-sm text-gray-600 mb-2">Administrators</div>
            <div class="text-3xl font-bold text-purple-600">{{ App\Models\User::role(['super_admin', 'admin'])->count() }}</div>
        </div>
        <div class="bg-white shadow rounded p-6">
            <div class="text-sm text-gray-600 mb-2">Church Staff</div>
            <div class="text-3xl font-bold text-green-600">{{ App\Models\User::role(['pastor', 'treasurer'])->count() }}</div>
        </div>
        <div class="bg-white shadow rounded p-6">
            <div class="text-sm text-gray-600 mb-2">Department Leaders</div>
            <div class="text-3xl font-bold text-orange-600">{{ App\Models\User::role('department_leader')->count() }}</div>
        </div>
    </div>

    @if(session('status'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    {{-- Users Table --}}
    @if($users->count())
    <div class="bg-white shadow rounded overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">User</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Role</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Joined</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr class="border-t hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <span class="text-white font-semibold">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                                <div class="ml-3">
                                    <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                    @if($user->id === auth()->id())
                                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded">You</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm text-gray-900">{{ $user->email }}</p>
                            @if($user->email_verified_at)
                            <span class="text-xs text-green-600">✓ Verified</span>
                            @else
                            <span class="text-xs text-gray-400">Not verified</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @foreach($user->roles as $role)
                            <span class="inline-block px-2 py-1 text-xs rounded font-medium
                                @if($role->name == 'super_admin') bg-red-100 text-red-800
                                @elseif($role->name == 'admin') bg-purple-100 text-purple-800
                                @elseif($role->name == 'pastor') bg-blue-100 text-blue-800
                                @elseif($role->name == 'treasurer') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                            </span>
                            @endforeach
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm text-gray-600">{{ $user->created_at->format('M d, Y') }}</p>
                            <p class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center space-x-2">
                                @can('view', $user)
                                <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:text-blue-900" title="View">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @endcan
                                @can('update', $user)
                                <a href="{{ route('users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @endcan
                                @can('delete', $user)
                                @if($user->id !== auth()->id())
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">
        {{ $users->links() }}
    </div>
    @else
    <div class="bg-white shadow rounded p-8 text-center">
        <p class="text-gray-500">No users found</p>
    </div>
    @endif
</div>
@endsection
