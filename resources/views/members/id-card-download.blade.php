@php
    $logoUrl = str_starts_with($churchLogo, 'images/') ? asset($churchLogo) : asset('storage/' . $churchLogo);
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Member ID Card - {{ $member->full_name }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .id-card {
            width: 350px;
            height: 550px;
            background: #ffffff;
            border-radius: 15px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border: 1px solid rgba(0,0,0,0.08);
            box-sizing: border-box;
        }
        .watermark {
            position: absolute;
            top: 55%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 220px;
            opacity: 0.04;
            pointer-events: none;
            z-index: 0;
        }
        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #0d9488 100%);
            height: 140px;
            position: relative;
            color: white;
            padding: 20px 15px 15px 15px;
            border-bottom: 3px solid #d97706;
            box-sizing: border-box;
        }
        .logo-wrapper {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 50%;
            padding: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
        }
        .logo {
            width: 100%;
            height: auto;
            max-height: 44px;
            object-fit: contain;
        }
        .church-info {
            margin-left: 65px;
            text-align: right;
        }
        .church-name {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin: 0 0 3px 0;
            line-height: 1.3;
            color: #ffffff;
        }
        .card-label {
            font-size: 8px;
            font-weight: 600;
            color: #fef08a;
            letter-spacing: 1px;
            margin: 0;
            text-transform: uppercase;
        }
        .chip {
            position: absolute;
            bottom: -20px;
            left: 25px;
            width: 45px;
            height: 35px;
            background: linear-gradient(135deg, #d4af37 0%, #f9d976 50%, #d4af37 100%);
            border-radius: 6px;
            border: 1px solid #b8860b;
            box-shadow: inset 0 0 5px rgba(0,0,0,0.2), 0 4px 6px rgba(0,0,0,0.1);
            z-index: 12;
        }
        .chip::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: rgba(0,0,0,0.15);
        }
        .chip::after {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 1px;
            background: rgba(0,0,0,0.15);
        }
        .chip-inner {
            position: absolute;
            top: 20%;
            left: 20%;
            right: 20%;
            bottom: 20%;
            border: 1px solid rgba(0,0,0,0.15);
            border-radius: 3px;
        }
        .photo-container {
            margin-top: -50px;
            text-align: center;
            position: relative;
            z-index: 10;
        }
        .photo {
            width: 125px;
            height: 125px;
            border-radius: 50%;
            border: 5px solid white;
            object-fit: cover;
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            background: white;
            box-sizing: border-box;
        }
        .initials-avatar {
            width: 115px;
            height: 115px;
            border-radius: 50%;
            border: 5px solid white;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1e3a8a 0%, #0d9488 100%);
            color: white;
            font-size: 42px;
            font-weight: 800;
            text-transform: uppercase;
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            box-sizing: border-box;
        }
        .details {
            text-align: center;
            padding: 15px 24px;
            box-sizing: border-box;
            position: relative;
            z-index: 1;
        }
        .name {
            font-size: 21px;
            font-weight: bold;
            color: #1a202c;
            margin: 10px 0 3px 0;
            letter-spacing: -0.3px;
        }
        .member-number {
            color: #4b5563;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 2px;
            margin: 0 0 20px 0;
            text-transform: uppercase;
            font-family: monospace;
        }
        .info-grid {
            display: flex;
            justify-content: space-between;
            border-top: 1px solid rgba(0,0,0,0.06);
            padding-top: 15px;
            margin-bottom: 15px;
            text-align: left;
        }
        .info-item {
            flex: 1;
        }
        .info-item.right {
            text-align: right;
        }
        .info-item label {
            display: block;
            font-size: 9px;
            font-weight: 700;
            color: #888;
            text-transform: uppercase;
            margin-bottom: 2px;
            letter-spacing: 0.5px;
        }
        .info-item span {
            font-size: 13px;
            font-weight: bold;
            color: #1a202c;
        }
        .info-item span.primary {
            color: #1e3a8a;
        }
        .dept-box {
            background-color: #f8fafc;
            border: 1px solid rgba(0,0,0,0.03);
            padding: 8px 10px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }
        .dept-box label {
            display: block;
            font-size: 9px;
            font-weight: 700;
            color: #888;
            text-transform: uppercase;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }
        .dept-box span {
            font-size: 13px;
            font-weight: bold;
            color: #4b5563;
        }
        .qr-code {
            position: absolute;
            bottom: 40px;
            left: 0;
            right: 0;
            text-align: center;
            z-index: 10;
        }
        .qr-wrapper {
            display: inline-block;
            padding: 4px;
            background: white;
            border-radius: 6px;
            border: 1px solid rgba(0,0,0,0.08);
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .qr-img {
            width: 70px;
            height: 70px;
            display: block;
        }
        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            background: #f1f5f9;
            padding: 8px 0;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            color: #4b5563;
            border-top: 1px solid rgba(0,0,0,0.06);
            z-index: 5;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>
    <div class="id-card">
        <!-- Watermark -->
        <img src="{{ $logoUrl }}" class="watermark" alt="Watermark">

        <!-- Header -->
        <div class="header">
            <div class="logo-wrapper">
                <img src="{{ $logoUrl }}" class="logo" alt="Logo">
            </div>
            <div class="church-info">
                <div class="church-name">{{ $churchName }}</div>
                <div class="card-label">Kadi Rasmi ya Mwanachama</div>
            </div>
            
            <!-- Smart Chip -->
            <div class="chip">
                <div class="chip-inner"></div>
            </div>
        </div>

        <!-- Photo -->
        <div class="photo-container">
            @if($member->profile_photo)
                <img src="{{ asset('storage/' . $member->profile_photo) }}" class="photo" alt="Profile Photo">
            @else
                <div class="initials-avatar">
                    {{ strtoupper(substr($member->full_name, 0, 1)) }}
                </div>
            @endif
        </div>

        <!-- Details -->
        <div class="details">
            <div class="name">{{ $member->full_name }}</div>
            <div class="member-number">{{ $member->member_number }}</div>

            <div class="info-grid">
                <div class="info-item">
                    <label>DHAMANA</label>
                    <span class="primary">Mwanachama</span>
                </div>
                <div class="info-item right">
                    <label>TANGU</label>
                    <span>{{ $member->created_at->format('M Y') }}</span>
                </div>
            </div>

            <div class="dept-box">
                <label>IDARA / KANDA</label>
                <span>{{ $member->departments->first()->name ?? 'Ushirika Mkuu' }}</span>
            </div>
        </div>

        <!-- QR Code -->
        <div class="qr-code">
            <div class="qr-wrapper">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode(route('attendance.scan-qr', $member->member_number)) }}" class="qr-img" alt="QR Code" style="width:100px;height:100px;">
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            www.manzesesdachurch.org
        </div>
    </div>
</body>
</html>
