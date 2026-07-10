@extends('layouts.admin')

@section('title', 'Lipa Online (M-Pesa / Card / Benki)')

@push('styles')
<style>
    .payment-pills .nav-link {
        background-color: #ffffff !important;
        color: #495057 !important;
        border: 2px solid #dee2e6 !important;
        border-radius: 12px !important;
        font-size: 1.15rem !important;
        font-weight: 700 !important;
        padding: 16px 20px !important;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05) !important;
        transition: all 0.3s ease !important;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .payment-pills .nav-link.active {
        background: linear-gradient(135deg, #007bff, #0056b3) !important;
        color: #ffffff !important;
        border-color: #0056b3 !important;
        box-shadow: 0 6px 12px rgba(0,123,255,0.25) !important;
    }
    .payment-pills .nav-link:hover:not(.active) {
        background-color: #f8f9fa !important;
        color: #007bff !important;
        border-color: #007bff !important;
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-header p-0 border-bottom-0">
                    <h4 class="px-4 pt-3 m-0 font-weight-bold text-dark"><i class="fas fa-hand-holding-usd text-primary"></i> Lipa / Toa Online (Online Giving)</h4>
                    <p class="px-4 text-muted small mb-3">Wasilisha Zaka, Sadaka au michango ya Ahadi kwa M-Pesa au Kadi ya Benki</p>
                    
                    <!-- Redesigned Two Choice Buttons -->
                    <ul class="nav nav-pills nav-fill payment-pills px-4 pb-3" id="paymentTabs" role="tablist" style="gap: 15px;">
                        <li class="nav-item">
                            <a class="nav-link active" id="general-tab" data-toggle="pill" href="#general-pane" role="tab" aria-selected="true">
                                <i class="fas fa-church mr-2"></i> Sadaka & Zaka
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pledge-tab" data-toggle="pill" href="#pledge-pane" role="tab" aria-selected="false">
                                <i class="fas fa-award mr-2"></i> Ahadi Zangu (Pledges)
                            </a>
                        </li>
                    </ul>
                </div>
                
                <form action="{{ route('give.process') }}" method="POST" id="payment-form">
                    @csrf
                    
                    <!-- Hidden field to pass payment type -->
                    <input type="hidden" name="payment_type" id="payment_type" value="general">
                    
                    <div class="card-body mt-2">
                        @if (session('error'))
                            <div class="alert alert-danger shadow-sm">
                                <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
                            </div>
                        @endif

                        <div class="tab-content" id="paymentTabsContent">
                            
                            <!-- GENERAL GIVING PANE -->
                            <div class="tab-pane fade show active" id="general-pane" role="tabpanel">
                                <div class="form-group">
                                    <label for="category" class="font-weight-bold">Aina ya Sadaka/Mchango <span class="text-danger">*</span></label>
                                    <select name="category" id="category" class="form-control form-control-lg" required>
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
                                        <small><i class="fas fa-info-circle mr-1"></i> Kiasi kilichobaki kulipwa kwenye ahadi hii: <strong>TSh <span id="pledge-balance-text">0</span></strong></small>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- COMMON FIELDS -->
                        <div class="row mt-4 pt-3 border-top">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="amount" class="font-weight-bold">Kiasi cha Fedha (TZS) <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg shadow-sm">
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
                                    <input type="text" name="phone_number" id="phone_number" class="form-control form-control-lg shadow-sm" 
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

                        <div class="alert alert-info shadow-sm mb-0 mt-3">
                            <div class="d-flex">
                                <div class="mr-3 pt-1">
                                    <i class="fas fa-lock fa-2x"></i>
                                </div>
                                <div>
                                    <h6 class="font-weight-bold mb-1">Muamala Salama (Secure Checkout)</h6>
                                    <small>Utaelekezwa kwenye ukurasa salama wa Flutterwave/Pesapal kukamilisha malipo kwa kutumia **M-Pesa, Airtel Money, Tigo Pesa, Halopesa** au **Kadi ya Benki**.</small>
                                </div>
                            </div>
                        </div>

                    </div>
                    
                    <div class="card-footer bg-white border-top">
                        <button type="submit" class="btn btn-primary btn-lg btn-block py-3 font-weight-bold shadow-sm">
                            <i class="fas fa-credit-card mr-1"></i> {{ __('KUENDELEA NA MALIPO ONLINE') }}
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
            } else {
                paymentType = 'general';
                $('#category').prop('required', true);
                $('#pledge_id').prop('required', false);
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
