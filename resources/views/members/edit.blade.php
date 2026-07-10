@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ __('Edit Member') }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('members.index') }}">{{ __('Members') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('members.show', $member) }}">{{ $member->full_name }}</a></li>
                    <li class="breadcrumb-item active">{{ __('Edit') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <!-- Form -->
                <form action="{{ route('members.update', $member) }}" method="POST" enctype="multipart/form-data" id="memberForm">
                    @csrf
                    @method('PUT')

                    <!-- Nav Tabs -->
                    <div class="card card-primary card-tabs">
                        <div class="card-header p-0 pt-1">
                            <ul class="nav nav-tabs" id="member-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="personal-tab" data-toggle="pill" href="#personal" role="tab">
                                        <i class="fas fa-user"></i> {{ __('Personal Info') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="contact-tab" data-toggle="pill" href="#contact" role="tab">
                                        <i class="fas fa-address-book"></i> {{ __('Contact') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="spiritual-tab" data-toggle="pill" href="#spiritual" role="tab">
                                        <i class="fas fa-cross"></i> {{ __('Spiritual') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="emergency-tab" data-toggle="pill" href="#emergency" role="tab">
                                        <i class="fas fa-exclamation-triangle"></i> {{ __('Emergency') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="photo-tab" data-toggle="pill" href="#photo" role="tab">
                                        <i class="fas fa-camera"></i> {{ __('Photo') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="departments-tab" data-toggle="pill" href="#departments" role="tab">
                                        <i class="fas fa-users"></i> {{ __('Departments') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="status-tab" data-toggle="pill" href="#status" role="tab">
                                        <i class="fas fa-cog"></i> {{ __('Status') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="member-tabContent">
                                <!-- Personal Information Tab -->
                                <div class="tab-pane fade show active" id="personal" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="full_name">
                                                    <i class="fas fa-user"></i> {{ __('Full Name') }} <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="full_name" id="full_name" 
                                                       class="form-control @error('full_name') is-invalid @enderror" 
                                                       value="{{ old('full_name', $member->full_name) }}" 
                                                       placeholder="{{ __('Enter full name') }}" required>
                                                @error('full_name')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                                <small class="form-text text-muted">{{ __('Member\'s complete legal name') }}</small>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="gender">
                                                    <i class="fas fa-venus-mars"></i> {{ __('Gender') }} <span class="text-danger">*</span>
                                                </label>
                                                <select name="gender" id="gender" 
                                                        class="form-control @error('gender') is-invalid @enderror" required>
                                                    <option value="">{{ __('Select Gender') }}</option>
                                                    <option value="male" {{ old('gender', $member->gender) == 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                                                    <option value="female" {{ old('gender', $member->gender) == 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                                                    <option value="other" {{ old('gender', $member->gender) == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                                                </select>
                                                @error('gender')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="date_of_birth">
                                                    <i class="fas fa-birthday-cake"></i> {{ __('Date of Birth') }} <span class="text-danger">*</span>
                                                </label>
                                                <input type="date" name="date_of_birth" id="date_of_birth" 
                                                       class="form-control @error('date_of_birth') is-invalid @enderror" 
                                                       value="{{ old('date_of_birth', $member->date_of_birth?->format('Y-m-d')) }}" 
                                                       max="{{ date('Y-m-d') }}" required>
                                                @error('date_of_birth')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                                <small class="form-text text-muted">{{ __('Used for birthday celebrations') }}</small>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="marital_status">
                                                    <i class="fas fa-heart"></i> {{ __('Marital Status') }} <span class="text-danger">*</span>
                                                </label>
                                                <select name="marital_status" id="marital_status" 
                                                        class="form-control @error('marital_status') is-invalid @enderror" required>
                                                    <option value="">{{ __('Select Status') }}</option>
                                                    <option value="single" {{ old('marital_status', $member->marital_status) == 'single' ? 'selected' : '' }}>{{ __('Single') }}</option>
                                                    <option value="married" {{ old('marital_status', $member->marital_status) == 'married' ? 'selected' : '' }}>{{ __('Married') }}</option>
                                                    <option value="widowed" {{ old('marital_status', $member->marital_status) == 'widowed' ? 'selected' : '' }}>{{ __('Widowed') }}</option>
                                                    <option value="divorced" {{ old('marital_status', $member->marital_status) == 'divorced' ? 'selected' : '' }}>{{ __('Divorced') }}</option>
                                                </select>
                                                @error('marital_status')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6" id="wedding_date_group" style="display: none;">
                                            <div class="form-group">
                                                <label for="wedding_date">
                                                    <i class="fas fa-ring"></i> {{ __('Wedding Date') }}
                                                </label>
                                                <input type="date" name="wedding_date" id="wedding_date" 
                                                       class="form-control @error('wedding_date') is-invalid @enderror" 
                                                       value="{{ old('wedding_date', $member->wedding_date?->format('Y-m-d')) }}" 
                                                       max="{{ date('Y-m-d') }}">
                                                @error('wedding_date')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                                <small class="form-text text-muted">{{ __('For anniversary celebrations') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact Information Tab -->
                                <div class="tab-pane fade" id="contact" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">
                                                    <i class="fas fa-envelope"></i> {{ __('Email Address') }} <span class="text-danger">*</span>
                                                </label>
                                                <input type="email" name="email" id="email" 
                                                       class="form-control @error('email') is-invalid @enderror" 
                                                       value="{{ old('email', $member->email) }}" 
                                                       placeholder="{{ __('email@example.com') }}" required>
                                                @error('email')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                                <small class="form-text text-muted">{{ __('Used for system notifications') }}</small>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone">
                                                    <i class="fas fa-phone"></i> {{ __('Phone Number') }}
                                                </label>
                                                <input type="text" name="phone" id="phone" 
                                                       class="form-control @error('phone') is-invalid @enderror" 
                                                       value="{{ old('phone', $member->phone) }}" 
                                                       placeholder="{{ __('+1234567890') }}">
                                                @error('phone')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                                <small class="form-text text-muted">{{ __('Include country code for international') }}</small>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="address">
                                                    <i class="fas fa-map-marker-alt"></i> {{ __('Address') }}
                                                </label>
                                                <textarea name="address" id="address" rows="3" 
                                                          class="form-control @error('address') is-invalid @enderror" 
                                                          placeholder="{{ __('Street address, city, state, zip code') }}">{{ old('address', $member->address) }}</textarea>
                                                @error('address')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                                <small class="form-text text-muted">{{ __('Current residential address') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Spiritual Journey Tab -->
                                <div class="tab-pane fade" id="spiritual" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="salvation_date">
                                                    <i class="fas fa-praying-hands"></i> {{ __('Salvation Date') }}
                                                </label>
                                                <input type="date" name="salvation_date" id="salvation_date" 
                                                       class="form-control @error('salvation_date') is-invalid @enderror" 
                                                       value="{{ old('salvation_date', $member->salvation_date?->format('Y-m-d')) }}" 
                                                       max="{{ date('Y-m-d') }}">
                                                @error('salvation_date')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                                <small class="form-text text-muted">{{ __('Day of accepting Christ') }}</small>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="baptism_date">
                                                    <i class="fas fa-water"></i> {{ __('Baptism Date') }}
                                                </label>
                                                <input type="date" name="baptism_date" id="baptism_date" 
                                                       class="form-control @error('baptism_date') is-invalid @enderror" 
                                                       value="{{ old('baptism_date', $member->baptism_date?->format('Y-m-d')) }}" 
                                                       max="{{ date('Y-m-d') }}">
                                                @error('baptism_date')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                                <small class="form-text text-muted">{{ __('Day of baptism') }}</small>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle"></i>
                                                <strong>{{ __('Note:') }}</strong> {{ __('Note: These dates help celebrate spiritual milestones and provide pastoral care.') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Emergency Contact Tab -->
                                <div class="tab-pane fade" id="emergency" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="emergency_contact_name">
                                                    <i class="fas fa-user-shield"></i> {{ __('Emergency Contact Name') }}
                                                </label>
                                                <input type="text" name="emergency_contact_name" id="emergency_contact_name" 
                                                       class="form-control @error('emergency_contact_name') is-invalid @enderror" 
                                                       value="{{ old('emergency_contact_name', $member->emergency_contact_name) }}" 
                                                       placeholder="{{ __('Full name of emergency contact') }}">
                                                @error('emergency_contact_name')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                                <small class="form-text text-muted">{{ __('Person to contact in emergency') }}</small>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="emergency_contact_phone">
                                                    <i class="fas fa-phone-alt"></i> {{ __('Emergency Contact Phone') }}
                                                </label>
                                                <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" 
                                                       class="form-control @error('emergency_contact_phone') is-invalid @enderror" 
                                                       value="{{ old('emergency_contact_phone', $member->emergency_contact_phone) }}" 
                                                       placeholder="{{ __('+1234567890') }}">
                                                @error('emergency_contact_phone')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                                <small class="form-text text-muted">{{ __('Contact person\'s phone') }}</small>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <strong>{{ __('Important:') }}</strong> {{ __('Important: Ensure emergency contact information is current and accurate.') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Profile Photo Tab -->
                                <div class="tab-pane fade" id="photo" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-12 text-center mb-3">
                                            @if($member->profile_photo)
                                                <img src="{{ asset('storage/' . $member->profile_photo) }}" 
                                                     alt="Current Profile Photo" 
                                                     class="img-circle img-fluid" 
                                                     id="current-photo"
                                                     style="width: 200px; height: 200px; object-fit: cover;">
                                            @else
                                                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" 
                                                     id="current-photo"
                                                     style="width: 200px; height: 200px;">
                                                    <i class="fas fa-user fa-5x text-white"></i>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="profile_photo">
                                                    <i class="fas fa-camera"></i> {{ __('Upload New Profile Photo') }}
                                                </label>
                                                <div class="custom-file">
                                                    <input type="file" name="profile_photo" id="profile_photo" 
                                                           class="custom-file-input @error('profile_photo') is-invalid @enderror" 
                                                           accept="image/*">
                                                    <label class="custom-file-label" for="profile_photo">{{ __('Choose file') }}</label>
                                                </div>
                                                @error('profile_photo')
                                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                                @enderror
                                                <small class="form-text text-muted">
                                                    {{ __('Recommended: Square image, at least 200x200px. Max 2MB. Formats: JPG, PNG, GIF') }}
                                                </small>
                                            </div>
                                        </div>

                                        <div class="col-md-12 text-center" id="preview-container" style="display: none;">
                                            <h5 class="mb-2">{{ __('Preview:') }}</h5>
                                            <img id="photo-preview" src="" alt="Photo Preview" 
                                                 class="img-circle img-fluid" 
                                                 style="width: 200px; height: 200px; object-fit: cover;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Departments Tab -->
                                <div class="tab-pane fade" id="departments" role="tabpanel">
                                    <div class="row">
                                        @if($departments && $departments->count() > 0)
                                            @foreach($departments as $department)
                                                <div class="col-md-6 mb-2">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" name="departments[]" value="{{ $department->id }}" 
                                                               id="dept_{{ $department->id }}" 
                                                               class="custom-control-input"
                                                               {{ $member->departments->contains($department->id) ? 'checked' : '' }}>
                                                        <label for="dept_{{ $department->id }}" class="custom-control-label">
                                                            <i class="fas fa-church"></i> {{ $department->name }}
                                                        </label>
                                                    </div>
                                                    @if($department->description)
                                                        <small class="form-text text-muted ml-4">{{ $department->description }}</small>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="col-md-12">
                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle"></i>
                                                    {{ __('No departments available. Create departments first.') }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Status Tab (Admin Only) -->
                                <div class="tab-pane fade" id="status" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="status">
                                                    <i class="fas fa-toggle-on"></i> {{ __('Member Status') }} <span class="text-danger">*</span>
                                                </label>
                                                <select name="status" id="status" 
                                                        class="form-control @error('status') is-invalid @enderror" required>
                                                    <option value="active" {{ old('status', $member->status) == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                                    <option value="inactive" {{ old('status', $member->status) == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                                    <option value="pending" {{ old('status', $member->status) == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                                </select>
                                                @error('status')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                                <small class="form-text text-muted">{{ __('Current membership status') }}</small>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="registration_type">
                                                    <i class="fas fa-id-card-alt"></i> {{ __('Aina ya Uanachama / Usajili') }}
                                                </label>
                                                <select name="registration_type" id="registration_type" 
                                                        class="form-control @error('registration_type') is-invalid @enderror">
                                                    <option value="Mshiriki Rasmi" {{ old('registration_type', $member->registration_type) == 'Mshiriki Rasmi' ? 'selected' : '' }}>Mshiriki Rasmi (Official Member)</option>
                                                    <option value="Muumini wa Kawaida" {{ old('registration_type', $member->registration_type) == 'Muumini wa Kawaida' ? 'selected' : '' }}>Muumini / Mhudhuriaji (Congregant / Attendee)</option>
                                                </select>
                                                @error('registration_type')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                                <small class="form-text text-muted">{{ __('Differentiate between official book member and regular visitor/attendee') }}</small>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="member_number">
                                                    <i class="fas fa-id-card"></i> {{ __('Member Number') }}
                                                </label>
                                                <input type="text" class="form-control" value="{{ $member->member_number }}" readonly disabled>
                                                <small class="form-text text-muted">{{ __('Auto-generated member ID') }}</small>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle"></i>
                                                <strong>{{ __('Member Information:') }}</strong><br>
                                                {{ __('Created:') }} {{ $member->created_at->format('F d, Y') }}<br>
                                                {{ __('Last Updated:') }} {{ $member->updated_at->format('F d, Y g:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Form Actions -->
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> {{ __('Update Member') }}
                                    </button>
                                    <a href="{{ route('members.show', $member) }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> {{ __('Cancel') }}
                                    </a>
                                    <a href="{{ route('members.index') }}" class="btn btn-outline-secondary float-right">
                                        <i class="fas fa-list"></i> {{ __('Back to List') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Show/hide wedding date based on marital status
    function toggleWeddingDate() {
        const maritalStatus = $('#marital_status').val();
        if (maritalStatus === 'married') {
            $('#wedding_date_group').slideDown();
        } else {
            $('#wedding_date_group').slideUp();
            $('#wedding_date').val('');
        }
    }
    
    // Initial check
    toggleWeddingDate();
    
    // Listen for changes
    $('#marital_status').change(toggleWeddingDate);

    // Photo preview
    $('#profile_photo').change(function() {
        const file = this.files[0];
        if (file) {
            // Update file label
            const fileName = file.name;
            $(this).next('.custom-file-label').html(fileName);

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#photo-preview').attr('src', e.target.result);
                $('#preview-container').slideDown();
            };
            reader.readAsDataURL(file);
        } else {
            $('#preview-container').slideUp();
            $(this).next('.custom-file-label').html('Choose file');
        }
    });

    // Form validation feedback
    $('#memberForm').submit(function(e) {
        // Show loading state
        $(this).find('button[type=submit]').prop('disabled', true).html(
            '<i class="fas fa-spinner fa-spin"></i> Updating...'
        );
    });
});
</script>
@endsection
