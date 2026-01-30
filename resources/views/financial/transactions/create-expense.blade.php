@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto bg-white shadow rounded p-6">
    <h1 class="text-2xl font-semibold mb-4">{{ __('Record Expense') }}</h1>
    
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('financial.expense.store') }}" method="POST">
        @csrf
        <div class="space-y-4">
            <div>
                <label class="block font-medium text-gray-700">{{ __('Category') }} <span class="text-red-500">*</span></label>
                <select name="category" class="mt-1 block w-full border-gray-300 rounded" required>
                    <option value="">{{ __('Select Category') }}</option>
                    @forelse($categories as $category)
                        <option value="{{ $category->name }}" {{ old('category') == $category->name ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @empty
                        {{-- Fallback if no categories defined --}}
                        <option value="Utilities" {{ old('category') == 'Utilities' ? 'selected' : '' }}>{{ __('Utilities (Electricity, Water, Internet)') }}</option>
                        <option value="Salaries & Allowances" {{ old('category') == 'Salaries & Allowances' ? 'selected' : '' }}>{{ __('Salaries & Allowances') }}</option>
                        <option value="Rent/Mortgage" {{ old('category') == 'Rent/Mortgage' ? 'selected' : '' }}>{{ __('Rent/Mortgage') }}</option>
                        <option value="Maintenance & Repairs" {{ old('category') == 'Maintenance & Repairs' ? 'selected' : '' }}>{{ __('Maintenance & Repairs') }}</option>
                        <option value="Ministry/Outreach" {{ old('category') == 'Ministry/Outreach' ? 'selected' : '' }}>{{ __('Ministry/Outreach') }}</option>
                        <option value="Equipment" {{ old('category') == 'Equipment' ? 'selected' : '' }}>{{ __('Equipment') }}</option>
                        <option value="Transportation" {{ old('category') == 'Transportation' ? 'selected' : '' }}>{{ __('Transportation') }}</option>
                        <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                    @endforelse
                </select>
                @if($categories->isEmpty())
                    <p class="text-xs text-gray-500 mt-1">
                        <a href="{{ route('giving-categories.index') }}" class="text-blue-600 hover:underline">{{ __('Add expense categories') }}</a> {{ __('to customize this list.') }}
                    </p>
                @endif
            </div>

            <div>
                <label class="block font-medium text-gray-700">{{ __('Amount') }} <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="mt-1 block w-full border-gray-300 rounded" required>
            </div>

            <div>
                <label class="block font-medium text-gray-700">{{ __('Payment Method') }} <span class="text-red-500">*</span></label>
                <select name="payment_method" class="mt-1 block w-full border-gray-300 rounded" required>
                    <option value="">{{ __('Select Method') }}</option>
                    <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>{{ __('Cash') }}</option>
                    <option value="Bank Transfer" {{ old('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>{{ __('Bank Transfer') }}</option>
                    <option value="Mobile Money" {{ old('payment_method') == 'Mobile Money' ? 'selected' : '' }}>{{ __('Mobile Money') }}</option>
                    <option value="Cheque" {{ old('payment_method') == 'Cheque' ? 'selected' : '' }}>{{ __('Cheque') }}</option>
                    <option value="Card/POS" {{ old('payment_method') == 'Card/POS' ? 'selected' : '' }}>{{ __('Card/POS') }}</option>
                </select>
            </div>

            <div>
                <label class="block font-medium text-gray-700">{{ __('Transaction Date') }} <span class="text-red-500">*</span></label>
                <input type="date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 rounded" required>
            </div>

            <div>
                <label class="block font-medium text-gray-700">{{ __('Reference Number') }}</label>
                <input type="text" name="reference_number" value="{{ old('reference_number') }}" class="mt-1 block w-full border-gray-300 rounded">
            </div>

            <div>
                <label class="block font-medium text-gray-700">{{ __('Description') }} <span class="text-red-500">*</span></label>
                <textarea name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded" required>{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                {{ __('Record Expense') }}
            </button>
            <a href="{{ route('financial.dashboard') }}" class="ml-4 text-gray-600 hover:underline">{{ __('Cancel') }}</a>
        </div>
    </form>
</div>
@endsection
