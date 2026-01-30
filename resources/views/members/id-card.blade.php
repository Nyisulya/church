@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mt-4">
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-id-card"></i> Digital ID Card
                    </h3>
                    <div class="card-tools">
                        <button onclick="downloadIdCard()" class="btn btn-success btn-sm">
                            <i class="fas fa-download"></i> Download Image
                        </button>
                    </div>
                </div>
                <div class="card-body text-center" style="background: #f4f6f9;">
                    <div id="id-card-container" class="d-inline-block text-left shadow-lg" style="width: 350px; height: 550px; background: white; border-radius: 15px; overflow: hidden; position: relative; font-family: 'Arial', sans-serif;">
                        
                        <!-- Watermark -->
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 200px; opacity: 0.05; pointer-events: none; z-index: 0;">
                            <img src="{{ asset('storage/' . $churchLogo) }}" style="width: 100%;" alt="Watermark">
                        </div>

                        <!-- Header Design -->
                        <div style="background: linear-gradient(135deg, #003366 0%, #0056b3 100%); height: 140px; position: relative; color: white;">
                            <!-- Logo -->
                            <div style="position: absolute; top: 15px; left: 15px; width: 50px; height: 50px; background: white; border-radius: 50%; padding: 2px; display: flex; align-items: center; justify-content: center;">
                                <img src="{{ asset('storage/' . $churchLogo) }}" alt="Logo" style="width: 40px; height: auto;">
                            </div>
                            
                            <!-- Church Name -->
                            <div style="padding-top: 20px; margin-left: 75px; margin-right: 15px; text-align: right;">
                                <h5 style="font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px; line-height: 1.3;">{{ $churchName }}</h5>
                                <p style="font-size: 9px; opacity: 0.8; margin: 0; letter-spacing: 0.5px;">OFFICIAL MEMBER CARD</p>
                            </div>

                            <!-- Smart Chip -->
                            <div style="position: absolute; top: 85px; left: 25px; width: 45px; height: 35px; background: linear-gradient(135deg, #d4af37 0%, #f9d976 50%, #d4af37 100%); border-radius: 5px; border: 1px solid #b8860b; box-shadow: inset 0 0 5px rgba(0,0,0,0.2);">
                                <div style="position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: rgba(0,0,0,0.1);"></div>
                                <div style="position: absolute; left: 50%; top: 0; bottom: 0; width: 1px; background: rgba(0,0,0,0.1);"></div>
                                <div style="position: absolute; top: 20%; left: 20%; right: 20%; bottom: 20%; border: 1px solid rgba(0,0,0,0.1); border-radius: 2px;"></div>
                            </div>
                        </div>

                        <!-- Profile Photo -->
                        <div class="text-center" style="margin-top: -45px; position: relative; z-index: 10;">
                            <div style="width: 130px; height: 130px; border-radius: 50%; border: 5px solid white; overflow: hidden; margin: 0 auto; box-shadow: 0 4px 8px rgba(0,0,0,0.1); background: white;">
                                @if($member->photo_path)
                                    <img src="{{ asset('storage/' . $member->photo_path) }}" alt="Profile Photo" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <img src="{{ asset('dist/img/default-profile.png') }}" alt="Default Photo" style="width: 100%; height: 100%; object-fit: cover;">
                                @endif
                            </div>
                        </div>

                        <!-- Member Details -->
                        <div class="text-center mt-2 px-4" style="position: relative; z-index: 1;">
                            <h3 class="font-weight-bold text-dark mb-1" style="font-size: 22px;">{{ $member->full_name }}</h3>
                            <p class="text-muted mb-3" style="letter-spacing: 1px; font-size: 14px;">{{ $member->member_number }}</p>
                            
                            <div class="row mt-4 mb-3">
                                <div class="col-6 text-left pl-4">
                                    <small class="text-muted d-block" style="font-size: 10px; text-transform: uppercase;">ROLE</small>
                                    <span class="font-weight-bold text-primary" style="font-size: 14px;">Member</span>
                                </div>
                                <div class="col-6 text-right pr-4">
                                    <small class="text-muted d-block" style="font-size: 10px; text-transform: uppercase;">JOINED</small>
                                    <span class="font-weight-bold" style="font-size: 14px;">{{ $member->created_at->format('M Y') }}</span>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 text-center">
                                    <small class="text-muted d-block" style="font-size: 10px; text-transform: uppercase;">DEPARTMENT</small>
                                    <span class="font-weight-bold" style="font-size: 14px;">
                                        {{ $member->departments->first()->name ?? 'General Member' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- QR Code -->
                        <div class="text-center mt-auto pb-4" style="position: relative; z-index: 1;">
                            <div class="d-inline-block p-2 bg-white rounded border">
                                {!! QrCode::size(80)->generate($member->member_number) !!}
                            </div>
                        </div>
                        
                        <!-- Footer -->
                        <div style="background: #f8f9fa; padding: 10px; text-align: center; border-top: 1px solid #eee; position: absolute; bottom: 0; width: 100%;">
                            <small class="text-muted" style="font-size: 10px;">www.manzesesdachurch.org</small>
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
        scale: 3, // Higher resolution for better print quality
        useCORS: true,
        logging: false,
        backgroundColor: null
    }).then(canvas => {
        const link = document.createElement('a');
        link.download = '{{ $member->member_number }}_ID_Card.png';
        link.href = canvas.toDataURL("image/png");
        link.click();
    });
}
</script>
@endsection
