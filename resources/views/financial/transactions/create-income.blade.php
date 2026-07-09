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
            <!-- Member Search -->
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

            <!-- Transaction Date & Payment Method -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-gray-700">{{ __('Transaction Date') }} <span class="text-red-500">*</span></label>
                    <input type="date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 rounded" required style="border:1px solid #d1d5db; padding: 6px;">
                </div>

                <div>
                    <label class="block font-medium text-gray-700">{{ __('Payment Method') }} <span class="text-red-500">*</span></label>
                    <select name="payment_method" class="mt-1 block w-full border-gray-300 rounded" required style="border:1px solid #d1d5db; padding: 6px;">
                        <option value="">{{ __('Select Method') }}</option>
                        <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>{{ __('Cash') }}</option>
                        <option value="Bank Transfer" {{ old('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>{{ __('Bank Transfer') }}</option>
                        <option value="Mobile Money" {{ old('payment_method') == 'Mobile Money' ? 'selected' : '' }}>{{ __('Mobile Money') }}</option>
                        <option value="Cheque" {{ old('payment_method') == 'Cheque' ? 'selected' : '' }}>{{ __('Cheque') }}</option>
                        <option value="Card/POS" {{ old('payment_method') == 'Card/POS' ? 'selected' : '' }}>{{ __('Card/POS') }}</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-gray-700">{{ __('Reference Number') }}</label>
                    <input type="text" name="reference_number" value="{{ old('reference_number') }}" class="mt-1 block w-full border-gray-300 rounded" style="border:1px solid #d1d5db; padding: 6px;">
                </div>

                <div>
                    <label class="block font-medium text-gray-700">{{ __('Description') }}</label>
                    <textarea name="description" rows="1" class="mt-1 block w-full border-gray-300 rounded" style="border:1px solid #d1d5db; padding: 6px;">{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- Dynamic Mchanganuo wa Fedha -->
            <div class="mt-6 border-t pt-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">💰 Mchanganuo wa Fedha (Split Amounts)</h3>
                
                <div class="space-y-4" id="items-container">
                    <!-- Row Template (Initial 1st Row) -->
                    <div class="item-row bg-gray-50 p-4 rounded relative border border-gray-200" data-row-index="0">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Category') }} <span class="text-red-500">*</span></label>
                                <select name="items[0][category]" class="mt-1 block w-full border-gray-300 rounded" required style="border: 1px solid #d1d5db; padding: 6px;">
                                    <option value="">{{ __('Select Category') }}</option>
                                    @forelse($categories as $category)
                                        <option value="{{ $category->name }}">{{ $category->name }}</option>
                                    @empty
                                        <option value="Tithes">{{ __('Tithes') }}</option>
                                        <option value="Offering - General">{{ __('Offering - General') }}</option>
                                        <option value="Offering - Special">{{ __('Offering - Special') }}</option>
                                        <option value="Donations">{{ __('Donations') }}</option>
                                        <option value="Other">{{ __('Other') }}</option>
                                    @endforelse
                                </select>
                            </div>
                            
                            <div class="pledge-select-div hidden">
                                <label class="block text-sm font-medium text-gray-700">{{ __('Husisha na Ahadi ya Muumini') }}</label>
                                <select name="items[0][pledge_id]" class="pledge-select mt-1 block w-full border-gray-300 rounded" style="border: 1px solid #d1d5db; padding: 6px;">
                                    <option value="">{{ __('Select Pledge (Optional)') }}</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Amount') }} <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01" name="items[0][amount]" class="mt-1 block w-full border-gray-300 rounded amount-input" required style="border: 1px solid #d1d5db; padding: 6px;">
                            </div>
                        </div>
                        
                        <!-- Delete Button (Hidden for row 0) -->
                        <button type="button" class="remove-row-btn absolute top-2 right-2 text-red-500 hover:text-red-700 hidden" style="background:none; border:none;">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>

                <div class="mt-4 flex justify-between items-center">
                    <button type="button" id="add-row-btn" class="bg-blue-600 text-white px-3 py-1.5 rounded hover:bg-blue-700 text-sm">
                        <i class="fas fa-plus"></i> Ongeza Aina Nyingine ya Fedha
                    </button>
                    
                    <div class="bg-gray-100 px-4 py-2 rounded text-right">
                        <span class="font-bold text-gray-700">Jumla Kuu:</span>
                        <span class="font-bold text-lg text-green-700 ml-2">{{ $currencySymbol }} <span id="total-amount-display">0.00</span></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 border-t pt-4">
            <button type="submit" class="bg-green-600 text-white px-6 py-2.5 rounded hover:bg-green-700 font-semibold shadow">
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
    
    var itemsContainer = document.getElementById('items-container');
    var addRowBtn = document.getElementById('add-row-btn');
    var totalAmountDisplay = document.getElementById('total-amount-display');

    if (!searchInput || !suggestionsContainer || !hiddenInput || !itemsContainer || !addRowBtn) return;

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
            updateAllPledgeDropdowns(initialMemberId);
        }
    }

    // Dynamic row index tracker
    var rowIndex = 1;

    // Helper to populate pledge dropdown for a specific select element
    function populatePledgeSelect(selectElement, memberId) {
        selectElement.innerHTML = '<option value="">-- Chagua Ahadi ya Muumini (Optional) --</option>';
        var parentDiv = selectElement.closest('.pledge-select-div');
        
        var pledges = activePledges[memberId];
        if (pledges && pledges.length > 0) {
            pledges.forEach(function(pledge) {
                var remaining = Number(pledge.amount) - Number(pledge.amount_paid);
                var option = document.createElement('option');
                option.value = pledge.id;
                option.textContent = pledge.purpose + ' (Ahadi: ' + Number(pledge.amount).toLocaleString() + ' - Bado: ' + remaining.toLocaleString() + ')';
                selectElement.appendChild(option);
            });
            if (parentDiv) parentDiv.classList.remove('hidden');
        } else {
            if (parentDiv) parentDiv.classList.add('hidden');
        }
    }

    // Update all pledge selects in all rows
    function updateAllPledgeDropdowns(memberId) {
        var selects = document.querySelectorAll('.pledge-select');
        selects.forEach(function(select) {
            populatePledgeSelect(select, memberId);
        });
    }

    // Hide all pledge selects in all rows
    function hideAllPledgeDropdowns() {
        var divs = document.querySelectorAll('.pledge-select-div');
        divs.forEach(function(div) {
            div.classList.add('hidden');
        });
        var selects = document.querySelectorAll('.pledge-select');
        selects.forEach(function(select) {
            select.innerHTML = '<option value="">{{ __('Select Pledge (Optional)') }}</option>';
        });
    }

    // Add dynamic row
    addRowBtn.addEventListener('click', function() {
        var firstRow = document.querySelector('.item-row');
        if (!firstRow) return;

        var newRow = firstRow.cloneNode(true);
        newRow.setAttribute('data-row-index', rowIndex);

        // Update name attributes for the inputs/selects in the new row
        var categorySelect = newRow.querySelector('select[name^="items["][name$="][category]"]');
        if (categorySelect) {
            categorySelect.name = 'items[' + rowIndex + '][category]';
            categorySelect.value = '';
        }

        var pledgeSelect = newRow.querySelector('select[name^="items["][name$="][pledge_id]"]');
        if (pledgeSelect) {
            pledgeSelect.name = 'items[' + rowIndex + '][pledge_id]';
            pledgeSelect.value = '';
        }

        var amountInput = newRow.querySelector('input[name^="items["][name$="][amount]"]');
        if (amountInput) {
            amountInput.name = 'items[' + rowIndex + '][amount]';
            amountInput.value = '';
        }

        // Show the delete button for the new row and hook its event
        var removeBtn = newRow.querySelector('.remove-row-btn');
        if (removeBtn) {
            removeBtn.classList.remove('hidden');
            removeBtn.addEventListener('click', function() {
                newRow.remove();
                updateTotalSum();
            });
        }

        itemsContainer.appendChild(newRow);

        // Populate pledges for this new row if a member is currently selected
        var selectedMemberId = hiddenInput.value;
        if (selectedMemberId && pledgeSelect) {
            populatePledgeSelect(pledgeSelect, selectedMemberId);
        }

        rowIndex++;
        updateTotalSum();
    });

    // Calculate total sum of amount fields
    function updateTotalSum() {
        var total = 0;
        var amountInputs = document.querySelectorAll('.amount-input');
        amountInputs.forEach(function(input) {
            var val = parseFloat(input.value);
            if (!isNaN(val)) {
                total += val;
            }
        });
        if (totalAmountDisplay) {
            totalAmountDisplay.textContent = total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
    }

    // Listen to changes in amount inputs to update sum dynamically
    itemsContainer.addEventListener('input', function(e) {
        if (e.target.classList.contains('amount-input')) {
            updateTotalSum();
        }
    });

    function updateSuggestions() {
        var filter = searchInput.value.toLowerCase().trim();
        suggestionsContainer.innerHTML = '';

        if (filter === '') {
            suggestionsContainer.classList.add('hidden');
            clearButton.classList.add('hidden');
            hiddenInput.value = '';
            hideAllPledgeDropdowns();
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
                updateAllPledgeDropdowns(m.id);
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
        hideAllPledgeDropdowns();
        updateTotalSum();
        searchInput.focus();
    });
});
</script>
@endsection
