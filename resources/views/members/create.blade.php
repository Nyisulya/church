@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto bg-white shadow rounded p-6">
    <h1 class="text-2xl font-semibold mb-4">{{ __('Add New Member') }}</h1>
    <form action="{{ route('members.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-medium text-gray-700">{{ __('Full Name') }}</label>
                <input type="text" name="full_name" class="mt-1 block w-full border-gray-300 rounded" required>
            </div>
            <div>
                <label class="block font-medium text-gray-700">{{ __('Email') }}</label>
                <input type="email" name="email" class="mt-1 block w-full border-gray-300 rounded" required>
            </div>
            <div>
                <label class="block font-medium text-gray-700">{{ __('Password') }}</label>
                <div class="relative">
                    <input type="password" name="password" id="create_password" class="mt-1 block w-full border-gray-300 rounded pr-10" required>
                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer" id="toggleCreatePassword">
                        <i class="fas fa-eye text-gray-500"></i>
                    </span>
                </div>
            </div>
            <div>
                <label class="block font-medium text-gray-700">{{ __('Member Type') }}</label>
                <select name="member_type" class="mt-1 block w-full border-gray-300 rounded" required>
                    <option value="member">{{ __('Member') }}</option>
                    <option value="pastor">{{ __('Pastor') }}</option>
                    <option value="department_leader">{{ __('Department Leader') }}</option>
                </select>
            </div>
            <div>
                <label class="block font-medium text-gray-700">{{ __('Aina ya Uanachama') }}</label>
                <select name="registration_type" class="mt-1 block w-full border-gray-300 rounded">
                    <option value="Mshiriki Rasmi">{{ __('Mshiriki Rasmi (Official Member)') }}</option>
                    <option value="Muumini wa Kawaida">{{ __('Muumini / Mhudhuriaji (Congregant)') }}</option>
                </select>
            </div>
            <div>
                <label class="block font-medium text-gray-700">{{ __('Phone') }}</label>
                <input type="text" name="phone" class="mt-1 block w-full border-gray-300 rounded">
            </div>
            <div>
                <label class="block font-medium text-gray-700">{{ __('Gender') }}</label>
                <select name="gender" class="mt-1 block w-full border-gray-300 rounded">
                    <option value="male">{{ __('Male') }}</option>
                    <option value="female">{{ __('Female') }}</option>
                    <option value="other">{{ __('Other') }}</option>
                </select>
            </div>
            <div>
                <label class="block font-medium text-gray-700">{{ __('Birthdate') }}</label>
                <input type="date" name="date_of_birth" class="mt-1 block w-full border-gray-300 rounded">
            </div>
            <div>
                <label class="block font-medium text-gray-700">{{ __('Marital Status') }}</label>
                <select name="marital_status" class="mt-1 block w-full border-gray-300 rounded">
                    <option value="single">{{ __('Single') }}</option>
                    <option value="married">{{ __('Married') }}</option>
                    <option value="widowed">{{ __('Widowed') }}</option>
                    <option value="divorced">{{ __('Divorced') }}</option>
                </select>
            </div>
            <div class="col-span-2">
                <label class="block font-medium text-gray-700">{{ __('Address') }}</label>
                <textarea name="address" class="mt-1 block w-full border-gray-300 rounded"></textarea>
            </div>
            <div>
                <label class="block font-medium text-gray-700">{{ __('Salvation Date') }}</label>
                <input type="date" name="salvation_date" class="mt-1 block w-full border-gray-300 rounded">
            </div>
            <div>
                <label class="block font-medium text-gray-700">{{ __('Baptism Date') }}</label>
                <input type="date" name="baptism_date" class="mt-1 block w-full border-gray-300 rounded">
            </div>
            <div>
                <label class="block font-medium text-gray-700">{{ __('Profile Photo') }}</label>
                <input type="file" name="profile_photo" class="mt-1 block w-full">
            </div>
            <div>
                <label class="block font-medium text-gray-700">{{ __('Emergency Contact Phone') }}</label>
                <input type="text" name="emergency_contact_phone" class="mt-1 block w-full border-gray-300 rounded">
            </div>
            <div class="col-span-2">
                <label class="block font-medium text-gray-700 mb-2">{{ __('Departments') }}</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($departments as $department)
                        <div class="flex items-center">
                            <input type="checkbox" name="departments[]" value="{{ $department->id }}" id="dept_{{ $department->id }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <label for="dept_{{ $department->id }}" class="ml-2 text-gray-700">{{ $department->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="mt-6">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">{{ __('Create Member') }}</button>
            <a href="{{ route('members.index') }}" class="ml-4 text-gray-600 hover:underline">{{ __('Cancel') }}</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('toggleCreatePassword').addEventListener('click', function (e) {
        const password = document.getElementById('create_password');
        const icon = this.querySelector('i');
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });
</script>
@endpush
