<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sajili Mahudhurio - Manzese SDA</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .scan-card {
            width: 100%;
            max-width: 440px;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
        .animate-bounce {
            animation: bounce 1.5s infinite;
        }
        .animate-pulse {
            animation: pulse 2s infinite;
        }
        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
        }
        .list-group-item-action:hover {
            background-color: #f8fafc;
            border-color: #3b82f6 !important;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="scan-card">
        <!-- Header Status Background -->
        @if($status === 'success')
            <div class="text-center py-5" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                <i class="fas fa-check-circle fa-5x animate-bounce mb-3"></i>
                <h2 class="font-weight-bold mb-0" style="font-size: 24px; letter-spacing: -0.5px;">Imesajiliwa!</h2>
            </div>
        @elseif($status === 'warning')
            <div class="text-center py-5" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
                <i class="fas fa-exclamation-circle fa-5x mb-3"></i>
                <h2 class="font-weight-bold mb-0" style="font-size: 24px; letter-spacing: -0.5px;">Tayari Yupo!</h2>
            </div>
        @elseif($status === 'choose_event')
            <div class="text-center py-5" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: white;">
                <i class="fas fa-tasks fa-5x mb-3 animate-pulse"></i>
                <h2 class="font-weight-bold mb-0" style="font-size: 24px; letter-spacing: -0.5px;">Chagua Ibada</h2>
            </div>
        @elseif($status === 'login_required')
            <div class="text-center py-5" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white;">
                <i class="fas fa-user-lock fa-5x mb-3 animate-pulse"></i>
                <h2 class="font-weight-bold mb-0" style="font-size: 24px; letter-spacing: -0.5px;">Ingia Kwenye Mfumo</h2>
            </div>
        @else
            <div class="text-center py-5" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white;">
                <i class="fas fa-times-circle fa-5x mb-3"></i>
                <h2 class="font-weight-bold mb-0" style="font-size: 24px; letter-spacing: -0.5px;">Imefeli!</h2>
            </div>
        @endif

        <div class="card-body px-4 py-4">
            <!-- Message -->
            <p class="lead text-secondary text-center mb-4 font-weight-normal" style="font-size: 15px; line-height: 1.5;">{{ $message }}</p>

            <!-- Choose Event List (if multiple exist today) -->
            @if($status === 'choose_event')
                <div class="list-group mb-4">
                    @foreach($events as $eventItem)
                        <a href="{{ route('attendance.scan-qr', ['memberNumber' => $member->member_number, 'event_id' => $eventItem->id]) }}" class="list-group-item list-group-item-action text-left p-3 mb-2 shadow-sm" style="border-radius: 10px; border: 1px solid rgba(0,0,0,0.06); transition: all 0.2s ease-in-out;">
                            <span class="d-block font-weight-bold text-dark mb-1" style="font-size: 15px;">⛪ {{ $eventItem->name }}</span>
                            <small class="text-muted d-block" style="font-size: 12px;">📅 {{ $eventItem->date->format('d M Y') }} | ⏰ {{ \Carbon\Carbon::parse($eventItem->start_time)->format('h:i A') }}</small>
                        </a>
                    @endforeach
                </div>
            @endif

            <!-- Login Form (if login is required) -->
            @if($status === 'login_required')
                @if(isset($login_error))
                    <div class="alert alert-danger text-center py-2 mb-3" style="font-size: 13px; border-radius: 8px;">
                        <i class="fas fa-exclamation-triangle mr-1"></i> {{ $login_error }}
                    </div>
                @endif

                <form action="{{ route('attendance.scan-qr.login', $member->member_number) }}" method="POST" class="mb-4">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="email" class="text-secondary font-weight-bold mb-1" style="font-size: 11px;">BARUA PEPE (EMAIL)</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="Mhariri@domain.com" style="border-radius: 8px; height: 45px; font-size: 14px;" required>
                    </div>
                    <div class="form-group mb-4">
                        <label for="password" class="text-secondary font-weight-bold mb-1" style="font-size: 11px;">NENOSIRI (PASSWORD)</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" style="border-radius: 8px; height: 45px; font-size: 14px;" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block py-2.5 font-weight-bold shadow-sm" style="border-radius: 8px; height: 45px; font-size: 15px; background-color: #1e3a8a; border: none;">
                        <i class="fas fa-sign-in-alt mr-1"></i> Thibitisha & Sajili
                    </button>
                </form>
            @endif

            <!-- Member Details -->
            @if($member)
                <div class="p-3 mb-4 rounded text-left" style="background-color: #f8fafc; border: 1px solid rgba(0,0,0,0.05);">
                    <div class="d-flex align-items-center">
                        <div class="mr-3">
                            @if($member->profile_photo)
                                <img src="{{ asset('storage/' . $member->profile_photo) }}" alt="Profile" class="rounded-circle shadow-sm" style="width: 55px; height: 55px; object-fit: cover; border: 2px solid white;">
                            @else
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white font-weight-bold shadow-sm" style="width: 55px; height: 55px; background: linear-gradient(135deg, #1e3a8a 0%, #0d9488 100%); font-size: 22px;">
                                    {{ strtoupper(substr($member->full_name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h4 class="font-weight-bold mb-0 text-dark" style="font-size: 16px;">{{ $member->full_name }}</h4>
                            <span class="text-secondary font-weight-bold" style="font-size: 12px; font-family: monospace;">{{ $member->member_number }}</span>
                        </div>
                    </div>

                    <hr style="border-top: 1px dashed rgba(0,0,0,0.08); margin: 12px 0;">

                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted d-block text-uppercase" style="font-size: 8px; font-weight: 700; letter-spacing: 0.5px;">DHAMANA</small>
                            <span class="font-weight-bold text-primary" style="font-size: 12px;">Mwanachama</span>
                        </div>
                        <div class="col-6 text-right">
                            <small class="text-muted d-block text-uppercase" style="font-size: 8px; font-weight: 700; letter-spacing: 0.5px;">IDARA / KANDA</small>
                            <span class="font-weight-bold text-dark" style="font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">
                                {{ $member->departments->first()->name ?? 'Ushirika Mkuu' }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Event Details -->
            @if(isset($event) && $event && $status !== 'choose_event')
                <div class="mb-4 text-muted text-center" style="font-size: 13px;">
                    <span class="d-block font-weight-bold text-dark mb-1">⛪ {{ $event->name }}</span>
                    <span class="d-block mb-2">📅 {{ $event->date->format('d M Y') }} | ⏰ {{ \Carbon\Carbon::parse($event->start_time)->format('h:i A') }}</span>
                    
                    <!-- Change Event Link (if multiple exist) -->
                    @if(isset($show_change_event) && $show_change_event)
                        <a href="{{ route('attendance.scan-qr', ['memberNumber' => $member->member_number, 'clear_event' => 1]) }}" class="text-primary font-weight-bold" style="font-size: 12px;">
                            <i class="fas fa-exchange-alt mr-1"></i> Badilisha Ibada
                        </a>
                    @endif
                </div>
            @endif

            <!-- Action Buttons (Only show when not requesting login/choosing event) -->
            @if($status !== 'login_required' && $status !== 'choose_event')
                <div class="d-flex flex-column gap-2 mt-4">
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-block py-2.5 font-weight-bold mb-2 shadow-sm" style="border-radius: 8px; background-color: #1e3a8a; border: none; height: 45px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-home mr-1"></i> Fungua Mfumo (Dashboard)
                    </a>
                </div>
            @endif
        </div>
        
        <!-- Card Footer -->
        <div class="card-footer bg-light text-center py-3 border-0">
            <small class="text-secondary font-weight-bold" style="font-size: 11px;">Manzese Seventh-Day Adventist Church</small>
        </div>
    </div>

    <!-- Bootstrap & jQuery JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
