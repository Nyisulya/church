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
            background-color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .id-card {
            width: 350px;
            height: 550px;
            background: linear-gradient(135deg, #ffffff 0%, #f0f2f5 100%);
            border-radius: 15px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border: 1px solid #e1e4e8;
        }
        .header {
            background: linear-gradient(135deg, #003366 0%, #0056b3 100%);
            height: 140px;
            position: relative;
            color: white;
            text-align: center;
        }
        .logo {
            position: absolute;
            top: 15px;
            left: 15px;
            width: 50px;
            height: 50px;
            object-fit: contain;
            background: white;
            border-radius: 50%;
            padding: 2px;
        }
        .church-name {
            padding-top: 20px;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-left: 60px; /* Space for logo */
            margin-right: 15px;
            text-align: right;
            line-height: 1.2;
        }
        .card-title {
            font-size: 10px;
            opacity: 0.8;
            margin-left: 60px;
            margin-right: 15px;
            text-align: right;
            margin-top: 5px;
        }
        .chip {
            position: absolute;
            top: 80px;
            left: 25px;
            width: 45px;
            height: 35px;
            background: linear-gradient(135deg, #d4af37 0%, #f9d976 50%, #d4af37 100%);
            border-radius: 5px;
            border: 1px solid #b8860b;
            box-shadow: inset 0 0 5px rgba(0,0,0,0.2);
        }
        .chip::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: rgba(0,0,0,0.1);
        }
        .chip::after {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 1px;
            background: rgba(0,0,0,0.1);
        }
        .photo-container {
            margin-top: -40px;
            text-align: center;
            position: relative;
            z-index: 10;
        }
        .photo {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            border: 5px solid white;
            object-fit: cover;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            background: #fff;
        }
        .details {
            text-align: center;
            padding: 15px 20px;
        }
        .name {
            font-size: 22px;
            font-weight: bold;
            color: #333;
            margin: 10px 0 5px;
        }
        .member-id {
            color: #666;
            font-size: 14px;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
            text-align: left;
            padding: 0 10px;
        }
        .info-item label {
            display: block;
            font-size: 10px;
            color: #888;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .info-item span {
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }
        .qr-code {
            text-align: center;
            margin-top: 10px;
        }
        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            background: #f8f9fa;
            padding: 10px 0;
            text-align: center;
            font-size: 10px;
            color: #888;
            border-top: 1px solid #eee;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 200px;
            opacity: 0.05;
            pointer-events: none;
            z-index: 0;
        }
    </style>
</head>
<body>
    <div class="id-card">
        <!-- Watermark -->
        <img src="{{ asset('storage/' . $churchLogo) }}" class="watermark" alt="Watermark">

        <div class="header">
            <img src="{{ asset('storage/' . $churchLogo) }}" class="logo" alt="Logo">
            <div class="church-name">{{ $churchName }}</div>
            <div class="card-title">OFFICIAL MEMBER CARD</div>
            
            <!-- Smart Chip -->
            <div class="chip"></div>
        </div>

        <div class="photo-container">
            @if($member->photo_path)
                <img src="{{ asset('storage/' . $member->photo_path) }}" class="photo" alt="Profile Photo">
            @else
                <img src="{{ asset('dist/img/default-profile.png') }}" class="photo" alt="Default Photo">
            @endif
        </div>

        <div class="details">
            <h2 class="name">{{ $member->full_name }}</h2>
            <div class="member-id">{{ $member->member_number }}</div>

            <div class="info-grid">
                <div class="info-item">
                    <label>Role</label>
                    <span>Member</span>
                </div>
                <div class="info-item" style="text-align: right;">
                    <label>Joined</label>
                    <span>{{ $member->created_at->format('M Y') }}</span>
                </div>
                <div class="info-item" style="grid-column: 1 / -1; text-align: center;">
                    <label>Department</label>
                    <span>{{ $member->departments->first()->name ?? 'General Member' }}</span>
                </div>
            </div>

            <div class="qr-code">
                {!! QrCode::size(80)->generate($member->member_number) !!}
            </div>
        </div>

        <div class="footer">
            www.manzesesdachurch.org
        </div>
    </div>
</body>
</html>
