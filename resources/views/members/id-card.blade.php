@extends('layouts.admin')

@section('content')
@php
    $logoUrl = str_starts_with($churchLogo, 'images/') ? asset($churchLogo) : asset('storage/' . $churchLogo);
@endphp

<div class="container-fluid">
    <div class="row justify-content-center mt-4">
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-id-card mr-2"></i> Kadi ya Mwanachama (Digital ID Card)
                    </h3>
                    <div class="card-tools">
                        <button onclick="downloadIdCard()" class="btn btn-success btn-sm">
                            <i class="fas fa-download mr-1"></i> Pakua Kama Picha
                        </button>
                    </div>
                </div>
                <div class="card-body text-center" style="background: #f4f6f9; padding: 30px 15px;">
                    <!-- ID CARD CONTAINER -->
                    <div id="id-card-container" class="d-inline-block text-left shadow-lg" style="width: 350px; height: 550px; background: white; border-radius: 15px; overflow: hidden; position: relative; font-family: 'Inter', 'Segoe UI', Arial, sans-serif; border: 1px solid rgba(0,0,0,0.08);">
                        
                        <!-- Watermark Logo (faint background) -->
                        <div style="position: absolute; top: 55%; left: 50%; transform: translate(-50%, -50%); width: 220px; opacity: 0.04; pointer-events: none; z-index: 0;">
                            <img src="{{ $logoUrl }}" style="width: 100%;" alt="Watermark">
                        </div>

                        <!-- Header Design -->
                        <div style="background: linear-gradient(135deg, #1e3a8a 0%, #0d9488 100%); height: 140px; position: relative; color: white; padding: 20px 15px 15px 15px; border-bottom: 3px solid #d97706;">
                            <!-- Logo Wrapper -->
                            <div style="position: absolute; top: 20px; left: 20px; width: 50px; height: 50px; background: white; border-radius: 50%; padding: 3px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px rgba(0,0,0,0.15);">
                                <img src="{{ $logoUrl }}" alt="Logo" style="width: 100%; height: auto; max-height: 44px; object-fit: contain;">
                            </div>
                            
                            <!-- Church Branding Details -->
                            <div style="margin-left: 65px; text-align: right;">
                                <h5 style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; margin: 0 0 3px 0; line-height: 1.3; color: #ffffff;">{{ $churchName }}</h5>
                                <p style="font-size: 8px; font-weight: 600; color: #fef08a; letter-spacing: 1px; margin: 0; text-transform: uppercase;">Kadi Rasmi ya Mwanachama</p>
                            </div>

                            <!-- Integrated Smart Chip Graphic -->
                            <div style="position: absolute; bottom: -20px; left: 25px; width: 45px; height: 35px; background: linear-gradient(135deg, #d4af37 0%, #f9d976 50%, #d4af37 100%); border-radius: 6px; border: 1px solid #b8860b; box-shadow: inset 0 0 5px rgba(0,0,0,0.2), 0 4px 6px rgba(0,0,0,0.1); z-index: 12;">
                                <div style="position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: rgba(0,0,0,0.15);"></div>
                                <div style="position: absolute; left: 50%; top: 0; bottom: 0; width: 1px; background: rgba(0,0,0,0.15);"></div>
                                <div style="position: absolute; top: 20%; left: 20%; right: 20%; bottom: 20%; border: 1px solid rgba(0,0,0,0.15); border-radius: 3px;"></div>
                            </div>
                        </div>

                        <!-- Profile Photo Section -->
                        <div class="text-center" style="margin-top: -50px; position: relative; z-index: 10;">
                            <div style="width: 125px; height: 125px; border-radius: 50%; border: 5px solid white; overflow: hidden; margin: 0 auto; box-shadow: 0 6px 12px rgba(0,0,0,0.15); background: white;">
                                @if($member->profile_photo)
                                    <img src="{{ asset('storage/' . $member->profile_photo) }}" alt="Profile Photo" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <!-- Initials Avatar Fallback -->
                                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #1e3a8a 0%, #0d9488 100%); color: white; font-size: 42px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">
                                        {{ strtoupper(substr($member->full_name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Member Name & ID Details -->
                        <div class="text-center mt-3 px-4" style="position: relative; z-index: 1;">
                            <h3 class="font-weight-bold text-dark mb-1" style="font-size: 21px; letter-spacing: -0.3px;">{{ $member->full_name }}</h3>
                            <p class="text-secondary font-weight-bold" style="font-size: 12px; letter-spacing: 2px; font-family: monospace; text-transform: uppercase; color: #4b5563 !important;">
                                {{ $member->member_number }}
                            </p>
                            
                            <!-- Information Grid -->
                            <div class="row mt-4 mb-3 border-top pt-3" style="border-color: rgba(0,0,0,0.06) !important;">
                                <div class="col-6 text-left pl-3">
                                    <small class="text-muted d-block" style="font-size: 9px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">DHAMANA</small>
                                    <span class="font-weight-bold text-primary" style="font-size: 13px;">Mwanachama</span>
                                </div>
                                <div class="col-6 text-right pr-3">
                                    <small class="text-muted d-block" style="font-size: 9px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">TANGU</small>
                                    <span class="font-weight-bold text-dark" style="font-size: 13px;">{{ $member->created_at->format('M Y') }}</span>
                                </div>
                            </div>

                            <div class="row pt-2 pb-2 rounded" style="background-color: #f8fafc; border: 1px solid rgba(0,0,0,0.03);">
                                <div class="col-12 text-center">
                                    <small class="text-muted d-block mb-1" style="font-size: 9px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">IDARA / KANDA</small>
                                    <span class="font-weight-bold text-secondary" style="font-size: 13px;">
                                        {{ $member->departments->first()->name ?? 'Ushirika Mkuu' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Secure QR Code (via Reliable API) -->
                        <div style="position: absolute; bottom: 38px; left: 0; right: 0; text-align: center; z-index: 10;">
                            <div class="d-inline-block p-1 bg-white rounded border shadow-sm" style="border-color: rgba(0,0,0,0.08) !important;">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($member->member_number) }}" alt="QR Code" style="width: 100px; height: 100px; display: block;">
                            </div>
                        </div>
                        
                        <!-- Footer Branding -->
                        <div style="background: #f1f5f9; padding: 8px 10px; text-align: center; border-top: 1px solid rgba(0,0,0,0.06); position: absolute; bottom: 0; width: 100%; z-index: 5;">
                            <small class="text-secondary font-weight-bold" style="font-size: 9px; letter-spacing: 0.5px;">www.manzesesdachurch.org</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- html2canvas Library -->
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script>
function downloadIdCard() {
    const element = document.getElementById("id-card-container");
    
    html2canvas(element, {
        scale: 3, // High DPI for professional prints
        useCORS: true,
        logging: false,
        backgroundColor: null
    }).then(canvas => {
        const link = document.createElement('a');
        link.download = 'ID_CARD_{{ $member->member_number }}.png';
        link.href = canvas.toDataURL("image/png");
        link.click();
    });
}
</script>
@endsection
