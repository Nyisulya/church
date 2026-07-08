@extends('layouts.admin')

@section('title', 'Lipa Online (M-Pesa / Card / Benki)')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <div class="card card-primary card-outline card-outline-tabs">
                <div class="card-header p-0 border-bottom-0">
                    <h4 class="px-4 pt-3 m-0 font-weight-bold text-dark"><i class="fas fa-hand-holding-usd text-primary"></i> Lipa / Toa Online (Online Giving)</h4>
                    <p class="px-4 text-muted small">Wasilisha Zaka, Sadaka au michango ya Ahadi na Kanda kwa M-Pesa au Kadi ya Benki</p>
                    
                    <ul class="nav nav-tabs px-3" id="paymentTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="general-tab" data-toggle="pill" href="#general-pane" role="tab" aria-selected="true">
                                <i class="fas fa-church"></i> Sadaka & Zaka
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pledge-tab" data-toggle="pill" href="#pledge-pane" role="tab" aria-selected="false">
                                <i class="fas fa-award"></i> Ahadi Zangu (Pledges)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="group-tab" data-toggle="pill" href="#group-pane" role="tab" aria-selected="false">
                                <i class="fas fa-user-friends"></i> Michango ya Kanda
                            </a>
                        </li>
                    </ul>
                </div>
                
                <form action="{{ route('give.process') }}" method="POST" id="payment-form">
                    @csrf
                    
                    <!-- Hidden field to pass payment type -->
                    <input type="hidden" name="payment_type" id="payment_type" value="general">
                    
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                            </div>
                        @endif

                        <div class="tab-content" id="paymentTabsContent">
                            
                            <!-- GENERAL GIVING PANE -->
                            <div class="tab-pane fade show active" id="general-pane" role="tabpanel">
                                <div class="form-group">
                                    <label for="category" class="font-weight-bold">Aina ya Sadaka/Mchango <span class="text-danger">*</span></label>
                                    <select name="category" id="category" class="form-control form-control-lg">
                                        <option value="">-- Chagua Aina --</option>
                                        @forelse($categories as $cat)
                                            <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                        @empty
                                            <option value="Zaka">Zaka (Tithe)</option>
                                            <option value="Sadaka ya Sabato">Sadaka ya Sabato (Sabbath Offering)</option>
                                            <option value="Ujenzi">Michango ya Ujenzi (Building Fund)</option>
                                            <option value="Shukrani">Sadaka ya Shukrani (Thanksgiving)</option>
                                            <option value="Kambi">Michango ya Makambi (Camp Meeting)</option>
                                        @endforelse
                                    </select>
                                    <span class="text-muted small">Chagua kundi ambalo mchango wako utawekwa.</span>
                                </div>
                            </div>
                            
                            <!-- PLEDGE PANE -->
                            <div class="tab-pane fade" id="pledge-pane" role="tabpanel">
                                <div class="form-group">
                                    <label for="pledge_id" class="font-weight-bold">Chagua Ahadi yako <span class="text-danger">*</span></label>
                                    <select name="pledge_id" id="pledge_id" class="form-control form-control-lg" onchange="updatePledgeDetails(this)">
                                        <option value="">-- Chagua Ahadi ya Kulipia --</option>
                                        @forelse($pledges as $pledge)
                                            <option value="{{ $pledge->id }}" data-balance="{{ $pledge->remaining_balance }}">
                                                {{ $pledge->purpose }} (Bao: TSh {{ number_format($pledge->remaining_balance) }})
                                            </option>
                                        @empty
                                            <option value="" disabled>Huna ahadi zilizo hai kwa sasa.</option>
                                        @endforelse
                                    </select>
                                    <div id="pledge-alert" class="alert alert-warning mt-2 d-none">
                                        <small><i class="fas fa-info-circle"></i> Kiasi kilichobaki kulipwa kwenye ahadi hii: <strong>TSh <span id="pledge-balance-text">0</span></strong></small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- KANDA OFFERING PANE -->
                            <div class="tab-pane fade" id="group-pane" role="tabpanel">
                                <div class="form-group">
                                    <label for="small_group_offering_id" class="font-weight-bold">Mchango wa Kanda (Small Group Offering) <span class="text-danger">*</span></label>
                                    <select name="small_group_offering_id" id="small_group_offering_id" class="form-control form-control-lg">
                                        <option value="">-- Chagua Mchango wa Kanda --</option>
                                        @forelse($smallGroupOfferings as $offering)
                                            <option value="{{ $offering->id }}">
                                                {{ $offering->name }} (Target: TSh {{ number_format($offering->target_amount) }})
                                            </option>
                                        @empty
                                            <option value="" disabled>Kanda yako haina michango ya kulipia iliyo hai kwa sasa.</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>

                        </div>

                        <!-- COMMON FIELDS -->
                        <div class="row mt-4 pt-3 border-top">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="amount" class="font-weight-bold">Kiasi cha Fedha (TZS) <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text font-weight-bold bg-light">TSh</span>
                                        </div>
                                        <input type="number" name="amount" id="amount" class="form-control font-weight-bold" 
                                               step="100" min="100" required placeholder="Ingiza Kiasi mfano: 10000">
                                    </div>
                                    <span class="text-muted small">Kiwango cha chini ni TSh 100</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone_number" class="font-weight-bold">Namba ya Simu ya Malipo</label>
                                    <input type="text" name="phone_number" id="phone_number" class="form-control form-control-lg" 
                                           placeholder="e.g. 0756XXXXXX" value="{{ Auth::user()->member->phone }}">
                                    <span class="text-muted small">Namba itakayotumika kutuma ombi (push prompt). Unaweza kulipia namba yoyote.</span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description" class="font-weight-bold">Maelezo ya Ziada (Hiari)</label>
                                    <textarea name="description" id="description" class="form-control" rows="2" placeholder="Andika maelezo mafupi kama yapo..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <div class="d-flex">
                                <div class="mr-3 pt-1">
                                    <i class="fas fa-lock fa-2x"></i>
                                </div>
                                <div>
                                    <h6 class="font-weight-bold mb-1">Muamala Salama (Secure Checkout)</h6>
                                    <small>Utaelekezwa kwenye ukurasa salama wa Flutterwave kukamilisha malipo kwa kutumia **M-Pesa, Airtel Money, Tigo Pesa, Halopesa** au **Kadi ya Benki**.</small>
                                </div>
                            </div>
                        </div>

                    </div>
                    
                    <div class="card-footer bg-white border-top">
                        <button type="submit" class="btn btn-primary btn-lg btn-block py-3 font-weight-bold">
                            <i class="fas fa-credit-card"></i> KINDA KUENDELEA NA MALIPO ONLINE
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Tab click event to change hidden input payment_type
        $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
            var targetId = $(e.target).attr('id');
            var paymentType = 'general';
            
            if (targetId === 'pledge-tab') {
                paymentType = 'pledge';
                $('#category').prop('required', false);
                $('#pledge_id').prop('required', true);
                $('#small_group_offering_id').prop('required', false);
            } else if (targetId === 'group-tab') {
                paymentType = 'small_group';
                $('#category').prop('required', false);
                $('#pledge_id').prop('required', false);
                $('#small_group_offering_id').prop('required', true);
            } else {
                paymentType = 'general';
                $('#category').prop('required', true);
                $('#pledge_id').prop('required', false);
                $('#small_group_offering_id').prop('required', false);
            }
            
            $('#payment_type').val(paymentType);
        });

        // Pre-select tab if preselectedPledge exists
        @if(isset($preselectedPledge))
            $('#pledge-tab').tab('show');
            $('#pledge_id').val('{{ $preselectedPledge->id }}').trigger('change');
            $('#amount').val('{{ (int)$preselectedPledge->remaining_balance }}');
        @endif
    });

    function updatePledgeDetails(select) {
        var selected = $(select).find('option:selected');
        var balance = selected.data('balance');
        
        if (balance) {
            $('#pledge-balance-text').text(new Intl.NumberFormat().format(balance));
            $('#pledge-alert').removeClass('d-none');
            $('#amount').val(balance);
        } else {
            $('#pledge-alert').addClass('d-none');
        }
    }
</script>
@endpush
@endsection
