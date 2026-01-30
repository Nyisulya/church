@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Create New User</h1>
            <p class="text-sm text-gray-600">Add a new user to the system with specific roles and permissions.</p>
        </div>
        <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center transition duration-150 ease-in-out">
            <i class="fas fa-arrow-left mr-2"></i> Back to Users
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900"><i class="fas fa-user-plus mr-2 text-blue-600"></i> User Details</h3>
        </div>
        
        <form action="{{ route('users.store') }}" method="POST" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2" for="name">Full Name <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="pl-10 w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror" placeholder="John Doe" required>
                    </div>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2" for="email">Email Address <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" class="pl-10 w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror" placeholder="john@example.com" required>
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role -->
                <div class="mb-4 md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-2" for="role">System Role <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user-tag text-gray-400"></i>
                        </div>
                        <select name="role" id="role" class="pl-10 w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('role') border-red-500 @enderror" required>
                            <option value="">Select a Role</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Assigning a role determines what this user can access in the system.</p>
                    @error('role')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Section Header -->
                <div class="md:col-span-2 mt-2 mb-2 border-t border-gray-200 pt-4">
                    <div class="flex justify-between items-center">
                        <h4 class="text-md font-medium text-gray-800"><i class="fas fa-lock mr-2 text-blue-600"></i> Security</h4>
                        <button type="button" id="generatePasswordBtn" class="text-sm bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-1 rounded transition duration-150">
                            <i class="fas fa-key mr-1"></i> Generate Password
                        </button>
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2" for="password">Password <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" name="password" id="password" class="pl-10 pr-10 w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror" required>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer" id="togglePassword">
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Min. 8 characters</p>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2" for="password_confirmation">Confirm Password <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-check-circle text-gray-400"></i>
                        </div>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="pl-10 w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end space-x-4 border-t border-gray-200 pt-6">
                <a href="{{ route('users.index') }}" class="px-6 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition duration-150 ease-in-out">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium shadow-md transition duration-150 ease-in-out flex items-center">
                    <i class="fas fa-save mr-2"></i> Create User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Password Visibility Toggle
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');
        
        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            passwordConfirmation.setAttribute('type', type); // Toggle both
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });

        // Generate Password
        const generateBtn = document.getElementById('generatePasswordBtn');
        generateBtn.addEventListener('click', function() {
            const length = 12;
            const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+";
            let retVal = "";
            for (let i = 0, n = charset.length; i < length; ++i) {
                retVal += charset.charAt(Math.floor(Math.random() * n));
            }
            
            password.value = retVal;
            passwordConfirmation.value = retVal;
            
            // Show password temporarily so user can see it
            if (password.getAttribute('type') === 'password') {
                togglePassword.click();
            }
        });
    });
</script>
@endpush
