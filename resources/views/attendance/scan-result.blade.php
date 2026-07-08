@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0" style="border-radius: 15px; overflow: hidden;">
                <!-- Header Status Background -->
                @if($status === 'success')
                    <div class="text-center py-5" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                        <i class="fas fa-check-circle fa-5x animate-bounce mb-3"></i>
                        <h2 class="font-weight-bold mb-0">Imesajiliwa!</h2>
                    </div>
                @elseif($status === 'warning')
                    <div class="text-center py-5" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
                        <i class="fas fa-exclamation-circle fa-5x mb-3"></i>
                        <h2 class="font-weight-bold mb-0">Tayari Yupo!</h2>
                    </div>
                @else
                    <div class="text-center py-5" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white;">
                        <i class="fas fa-times-circle fa-5x mb-3"></i>
                        <h2 class="font-weight-bold mb-0">Imefeli!</h2>
                    </div>
                @endif

                <div class="card-body px-4 py-4 text-center">
                    <!-- Message -->
                    <p class="lead text-secondary mb-4 font-weight-normal">{{ $message }}</p>

                    <!-- Member Details -->
                    @if($member)
                        <div class="p-3 mb-4 rounded text-left" style="background-color: #f8fafc; border: 1px solid rgba(0,0,0,0.05);">
                            <div class="d-flex align-items-center mb-3">
                                <div class="mr-3">
                                    @if($member->profile_photo)
                                        <img src="{{ asset('storage/' . $member->profile_photo) }}" alt="Profile" class="rounded-circle shadow-sm" style="width: 60px; height: 60px; object-fit: cover; border: 2px solid white;">
                                    @else
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white font-weight-bold shadow-sm" style="width: 60px; height: 60px; background: linear-gradient(135deg, #1e3a8a 0%, #0d9488 100%); font-size: 24px;">
                                            {{ strtoupper(substr($member->full_name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-weight-bold mb-0 text-dark" style="font-size: 18px;">{{ $member->full_name }}</h4>
                                    <span class="text-secondary font-weight-bold" style="font-size: 13px; font-family: monospace;">{{ $member->member_number }}</span>
                                </div>
                            </div>

                            <hr style="border-top: 1px dashed rgba(0,0,0,0.1); margin: 15px 0;">

                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted d-block text-uppercase" style="font-size: 9px; font-weight: 700; letter-spacing: 0.5px;">DHAMANA</small>
                                    <span class="font-weight-bold text-primary" style="font-size: 13px;">Mwanachama</span>
                                </div>
                                <div class="col-6 text-right">
                                    <small class="text-muted d-block text-uppercase" style="font-size: 9px; font-weight: 700; letter-spacing: 0.5px;">IDARA / KANDA</small>
                                    <span class="font-weight-bold text-dark" style="font-size: 13px;">
                                        {{ $member->departments->first()->name ?? 'Ushirika Mkuu' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Event Details -->
                    @if(isset($event) && $event)
                        <div class="mb-4 text-muted text-center" style="font-size: 14px;">
                            <span class="d-block font-weight-bold text-dark mb-1">⛪ {{ $event->name }}</span>
                            <span>📅 {{ $event->date->format('d M Y') }} | ⏰ {{ \Carbon\Carbon::parse($event->start_time)->format('h:i A') }}</span>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="d-flex flex-column gap-2 mt-4">
                        <a href="{{ route('attendance.index') }}" class="btn btn-primary btn-block py-2.5 font-weight-bold mb-2 shadow-sm" style="border-radius: 8px;">
                            <i class="fas fa-calendar-check mr-1"></i> Orodha ya Mahudhurio
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-block py-2.5 font-weight-bold" style="border-radius: 8px;">
                            <i class="fas fa-home mr-1"></i> Rudi Mwanzo
                        </a>
                    </div>
                </div>
                <!-- Card Footer Instruction -->
                <div class="card-footer bg-light text-center py-3 border-0">
                    <small class="text-secondary">Ushauri: Fungua kamera ya simu yako kuskani kadi nyingine mara moja.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    .animate-bounce {
        animation: bounce 1.5s infinite;
    }
</style>
@endsection
