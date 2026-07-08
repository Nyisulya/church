@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" style="border-radius: 8px;">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" style="border-radius: 8px;">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" style="border-radius: 8px;">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    <ul class="mb-0 pl-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card card-primary card-outline shadow">
                <div class="card-header bg-light">
                    <h3 class="card-title font-weight-bold text-dark mb-0">
                        <i class="fas fa-key text-primary mr-1"></i> {{ __('Change Password') }}
                    </h3>
                </div>
                <form action="{{ route('profile.change-password') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <p class="text-muted mb-4" style="font-size: 14px;">
                            <i class="fas fa-info-circle text-info mr-1"></i> 
                            {{ __('Please enter your current password and your new password to secure your account.') }}
                        </p>

                        <div class="form-group mb-4">
                            <label for="current_password" class="font-weight-bold text-secondary">
                                <i class="fas fa-unlock text-muted mr-1"></i> {{ __('Current Password') }}
                            </label>
                            <div class="input-group">
                                <input type="password" name="current_password" id="current_password" 
                                       class="form-control" placeholder="{{ __('Enter your current password...') }}" 
                                       style="border-radius: 8px 0 0 8px;" required>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary togglePassword" type="button" 
                                            data-target="current_password" style="border-radius: 0 8px 8px 0; border: 1px solid #ced4da;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group mb-0">
                                    <label for="new_password" class="font-weight-bold text-secondary">
                                        <i class="fas fa-lock text-muted mr-1"></i> {{ __('New Password') }}
                                    </label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="new_password" 
                                               class="form-control" placeholder="{{ __('At least 8 characters...') }}" 
                                               style="border-radius: 8px 0 0 8px;" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary togglePassword" type="button" 
                                                    data-target="new_password" style="border-radius: 0 8px 8px 0; border: 1px solid #ced4da;">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group mb-0">
                                    <label for="new_password_confirm" class="font-weight-bold text-secondary">
                                        <i class="fas fa-check text-muted mr-1"></i> {{ __('Confirm New Password') }}
                                    </label>
                                    <div class="input-group">
                                        <input type="password" name="password_confirmation" id="new_password_confirm" 
                                               class="form-control" placeholder="{{ __('Repeat new password...') }}" 
                                               style="border-radius: 8px 0 0 8px;" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary togglePassword" type="button" 
                                                    data-target="new_password_confirm" style="border-radius: 0 8px 8px 0; border: 1px solid #ced4da;">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right bg-light border-0">
                        <a href="{{ route('profile.index') }}" class="btn btn-secondary mr-2" style="border-radius: 8px;">
                            <i class="fas fa-times mr-1"></i> {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary font-weight-bold shadow-sm" 
                                style="border-radius: 8px; background-color: #1e3a8a; border: none;">
                            <i class="fas fa-save mr-1"></i> {{ __('Save Password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.togglePassword').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
</script>
@endpush
@endsection
