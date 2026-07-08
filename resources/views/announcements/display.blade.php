<!DOCTYPE html>
<html>
<head>
    <title>Church Announcements - Matangazo</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }
        .header h1 {
            font-size: 48px;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .header p {
            font-size: 24px;
            opacity: 0.9;
        }
        .announcement-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            animation: fadeIn 0.5s ease-in;
        }
        .announcement-card h2 {
            color: #667eea;
            font-size: 32px;
            margin-bottom: 15px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        .announcement-card .content {
            font-size: 22px;
            line-height: 1.6;
            color: #333;
            white-space: pre-line;
        }
        .announcement-card .date {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 16px;
            margin-top: 15px;
        }
        .no-announcements {
            text-align: center;
            color: white;
            font-size: 36px;
            padding: 100px 20px;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media print {
            body {
                background: white;
                padding: 20px;
            }
            .header {
                color: #333;
            }
            .announcement-card {
                page-break-inside: avoid;
                box-shadow: none;
                border: 2px solid #667eea;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📢 MATANGAZO</h1>
            <p>Church Announcements</p>
            <p style="font-size: 18px;">{{ now()->format('l, F j, Y') }}</p>
        </div>

        @forelse($announcements as $announcement)
            <div class="announcement-card">
                <h2>{{ $announcement->title }}</h2>
                <div class="content">{{ $announcement->body }}</div>
                <span class="date">{{ $announcement->announcement_date ? $announcement->announcement_date->format('F j, Y') : '' }}</span>
            </div>
        @empty
            <div class="no-announcements">
                <p>📭</p>
                <p>No announcements for this week</p>
                <p style="font-size: 20px; margin-top: 20px;">Hakuna matangazo kwa wiki hii</p>
            </div>
        @endforelse
    </div>
</body>
</html>
