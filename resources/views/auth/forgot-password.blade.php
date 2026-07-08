<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Manzese Seventh Day Adventist Church</title>
    
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #60a5fa 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            width: 100%;
            max-width: 450px;
        }
        
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        
        .church-logo {
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .church-name {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .church-tagline {
            font-size: 14px;
            opacity: 0.95;
            font-weight: 300;
        }
        
        .login-body {
            padding: 40px 30px;
        }
        
        .welcome-text {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .welcome-text h3 {
            color: #1e3a8a;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .welcome-text p {
            color: #64748b;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            color: #334155;
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .form-control {
            height: 50px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 0 20px;
            font-size: 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
        
        .btn-login {
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            margin-top: 10px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .footer-text {
            text-align: center;
            margin-top: 20px;
            color: #94a3b8;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="church-logo">
                    <img src="{{ asset('images/sda-logo.png') }}" alt="SDA Logo" style="width: 90px; height: 90px; object-fit: contain;">
                </div>
                <div class="church-name">MANZESE SDA CHURCH</div>
                <div class="church-tagline">{{ __('Forgot Password') }}</div>
            </div>
            
            <div class="login-body">
                <div class="welcome-text">
                    <h3>{{ __('Forget Password?') }}</h3>
                    <p>{{ __('Enter your email to receive a password reset link') }}</p>
                </div>
                
                @if (session('status'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-warning text-dark">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        @foreach ($errors->all() as $error)
                            <span>{{ $error }}</span>
                        @endforeach
                    </div>
                @endif
                
                <form action="{{ route('password.email') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label">{{ __('Email Address') }}</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               placeholder="{{ __('Enter your email') }}" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-login mb-3">
                        <i class="fas fa-paper-plane me-2"></i> {{ __('Send Reset Link') }}
                    </button>

                    <div class="text-center mt-2">
                        <a href="{{ route('login') }}" class="text-decoration-none text-primary font-weight-bold" style="font-size: 14px;">
                            <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Login') }}
                        </a>
                    </div>
                </form>
                
                <div class="footer-text">
                    &copy; {{ date('Y') }} Manzese Seventh Day Adventist Church
                </div>
            </div>
        </div>
    </div>
</body>
</html>
