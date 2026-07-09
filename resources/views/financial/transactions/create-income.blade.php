@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto bg-white shadow rounded p-6">
    <h1 class="text-2xl font-semibold mb-4">{{ __('Record Income') }}</h1>
    
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('financial.income.store') }}" method="POST">
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
                        <option value="Tithes" {{ old('category') == 'Tithes' ? 'selected' : '' }}>{{ __('Tithes') }}</option>
                        <option value="Offering - General" {{ old('category') == 'Offering - General' ? 'selected' : '' }}>{{ __('Offering - General') }}</option>
                        <option value="Offering - Special" {{ old('category') == 'Offering - Special' ? 'selected' : '' }}>{{ __('Offering - Special') }}</option>
                        <option value="Donations" {{ old('category') == 'Donations' ? 'selected' : '' }}>{{ __('Donations') }}</option>
                        <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                    @endforelse
                </select>
                @if($categories->isEmpty())
                    <p class="text-xs text-gray-500 mt-1">
                        <a href="{{ route('giving-categories.index') }}" class="text-blue-600 hover:underline">{{ __('Add categories') }}</a> {{ __('to customize this list.') }}
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

            <div class="relative" id="member-autocomplete-container">
                <label class="block font-medium text-gray-700">{{ __('Member') }} ({{ __('Optional') }})</label>
                <div class="relative">
                    <input type="text" id="member_search" class="mt-1 block w-full border-gray-300 rounded px-3 py-2 pr-10" placeholder="🔍 Anza kuandika jina la muumini..." autocomplete="off" style="border:1px solid #d1d5db;">
                    <button type="button" id="clear_member_search" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden" style="background:none; border:none; padding:0; outline:none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <input type="hidden" name="member_id" id="member_id_hidden" value="{{ old('member_id') }}">
                <div id="member_suggestions" class="absolute z-50 left-0 right-0 mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto hidden" style="border: 1px solid #d1d5db;">
                    <!-- Suggestions will be populated by Javascript -->
                </div>
            </div>

            <!-- Pledge Selection (Conditional) -->
            <div id="pledge_selection_container" class="hidden">
                <label class="block font-medium text-gray-700">{{ __('Husisha na Ahadi ya Muumini (Optional)') }}</label>
                <select name="pledge_id" id="pledge_id_select" class="mt-1 block w-full border-gray-300 rounded" style="border: 1px solid #d1d5db; padding: 8px;">
                    <option value="">{{ __('Select Pledge (Optional)') }}</option>
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
                <label class="block font-medium text-gray-700">{{ __('Description') }}</label>
                <textarea name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                {{ __('Record Income') }}
            </button>
            <a href="{{ route('financial.dashboard') }}" class="ml-4 text-gray-600 hover:underline">{{ __('Cancel') }}</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('member_search');
    var suggestionsContainer = document.getElementById('member_suggestions');
    var hiddenInput = document.getElementById('member_id_hidden');
    var clearButton = document.getElementById('clear_member_search');
    
    var pledgeContainer = document.getElementById('pledge_selection_container');
    var pledgeSelect = document.getElementById('pledge_id_select');

    if (!searchInput || !suggestionsContainer || !hiddenInput) return;

    var members = @json($members->map(function($m) {
        return [
            'id' => $m->id,
            'name' => trim($m->full_name) . ($m->member_number ? ' (' . trim($m->member_number) . ')' : '')
        ];
    }));

    var activePledges = @json($activePledges);

    var initialMemberId = hiddenInput.value;
    if (initialMemberId) {
        var found = members.find(function(m) { return m.id == initialMemberId; });
        if (found) {
            searchInput.value = found.name;
            clearButton.classList.remove('hidden');
            showPledgesForMember(initialMemberId);
        }
    }

    function showPledgesForMember(memberId) {
        if (!pledgeContainer || !pledgeSelect) return;
        
        pledgeSelect.innerHTML = '<option value="">-- Chagua Ahadi ya Muumini (Optional) --</option>';
        
        var pledges = activePledges[memberId];
        if (pledges && pledges.length > 0) {
            pledges.forEach(function(pledge) {
                var remaining = Number(pledge.amount) - Number(pledge.amount_paid);
                var option = document.createElement('option');
                option.value = pledge.id;
                option.textContent = pledge.purpose + ' (Ahadi: ' + Number(pledge.amount).toLocaleString() + ' - Bado: ' + remaining.toLocaleString() + ')';
                pledgeSelect.appendChild(option);
            });
            pledgeContainer.classList.remove('hidden');
        } else {
            pledgeContainer.classList.add('hidden');
        }
    }

    function updateSuggestions() {
        var filter = searchInput.value.toLowerCase().trim();
        suggestionsContainer.innerHTML = '';

        if (filter === '') {
            suggestionsContainer.classList.add('hidden');
            clearButton.classList.add('hidden');
            hiddenInput.value = '';
            if (pledgeContainer) pledgeContainer.classList.add('hidden');
            return;
        }

        clearButton.classList.remove('hidden');

        var matches = members.filter(function(m) {
            return m.name.toLowerCase().indexOf(filter) !== -1;
        });

        if (matches.length === 0) {
            var noResult = document.createElement('div');
            noResult.className = 'px-4 py-2 text-gray-500 italic text-sm';
            noResult.textContent = 'Hakuna mwanachama aliyepatikana';
            suggestionsContainer.appendChild(noResult);
            suggestionsContainer.classList.remove('hidden');
            return;
        }

        matches.forEach(function(m) {
            var item = document.createElement('div');
            item.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer text-gray-800 border-b last:border-b-0 text-sm';
            item.style.padding = '8px 12px';
            item.style.borderBottom = '1px solid #f3f4f6';
            item.style.cursor = 'pointer';
            item.textContent = m.name;
            item.addEventListener('click', function() {
                searchInput.value = m.name;
                hiddenInput.value = m.id;
                suggestionsContainer.classList.add('hidden');
                clearButton.classList.remove('hidden');
                showPledgesForMember(m.id);
            });
            suggestionsContainer.appendChild(item);
        });

        suggestionsContainer.classList.remove('hidden');
    }

    searchInput.addEventListener('input', updateSuggestions);

    searchInput.addEventListener('focus', function() {
        if (searchInput.value.trim() !== '') {
            updateSuggestions();
        }
    });

    document.addEventListener('click', function(e) {
        if (!document.getElementById('member-autocomplete-container').contains(e.target)) {
            suggestionsContainer.classList.add('hidden');
        }
    });

    clearButton.addEventListener('click', function() {
        searchInput.value = '';
        hiddenInput.value = '';
        clearButton.classList.add('hidden');
        suggestionsContainer.classList.add('hidden');
        if (pledgeContainer) pledgeContainer.classList.add('hidden');
        searchInput.focus();
    });
});
</script>
@endsection

