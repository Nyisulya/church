@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mt-4">
        <div class="col-md-10">
            <!-- Header -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-edit"></i> Edit My Profile
                    </h3>
                </div>
            </div>

            <!-- Warning Message for Incomplete Profile -->
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
                @csrf
                @method('PUT')

                <!-- Nav Tabs -->
                <div class="card card-primary card-tabs">
                    <div class="card-header p-0 pt-1">
                        <ul class="nav nav-tabs" id="profile-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="personal-tab" data-toggle="pill" href="#personal" role="tab">
                                    <i class="fas fa-user"></i> Personal Info
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="contact-tab" data-toggle="pill" href="#contact" role="tab">
                                    <i class="fas fa-address-book"></i> Contact
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="spiritual-tab" data-toggle="pill" href="#spiritual" role="tab">
                                    <i class="fas fa-cross"></i> Spiritual
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="emergency-tab" data-toggle="pill" href="#emergency" role="tab">
                                    <i class="fas fa-exclamation-triangle"></i> Emergency
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="photo-tab" data-toggle="pill" href="#photo" role="tab">
                                    <i class="fas fa-camera"></i> Photo
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="departments-tab" data-toggle="pill" href="#departments" role="tab">
                                    <i class="fas fa-users"></i> Departments
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="profile-tabContent">
                            <!-- Personal Information Tab -->
                            <div class="tab-pane fade show active" id="personal" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">
                                                <i class="fas fa-user"></i> Full Name <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="name" id="name" 
                                                   class="form-control @error('name') is-invalid @enderror" 
                                                   value="{{ old('name', $user->name) }}" 
                                                   placeholder="Enter your full name" required>
                                            @error('name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="form-text text-muted">This is your display name across the system</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="gender">
                                                <i class="fas fa-venus-mars"></i> Gender <span class="text-danger">*</span>
                                            </label>
                                            <select name="gender" id="gender" 
                                                    class="form-control @error('gender') is-invalid @enderror" required>
                                                <option value="">Select Gender</option>
                                                <option value="male" {{ old('gender', $member?->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                                <option value="female" {{ old('gender', $member?->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                                <option value="other" {{ old('gender', $member?->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            @error('gender')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date_of_birth">
                                                <i class="fas fa-birthday-cake"></i> Date of Birth <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" name="date_of_birth" id="date_of_birth" 
                                                   class="form-control @error('date_of_birth') is-invalid @enderror" 
                                                   value="{{ old('date_of_birth', $member?->date_of_birth?->format('Y-m-d')) }}" 
                                                   max="{{ date('Y-m-d') }}" required>
                                            @error('date_of_birth')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="form-text text-muted">Used for birthday celebrations</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="marital_status">
                                                <i class="fas fa-heart"></i> Marital Status <span class="text-danger">*</span>
                                            </label>
                                            <select name="marital_status" id="marital_status" 
                                                    class="form-control @error('marital_status') is-invalid @enderror" required>
                                                <option value="">Select Status</option>
                                                <option value="single" {{ old('marital_status', $member?->marital_status) == 'single' ? 'selected' : '' }}>Single</option>
                                                <option value="married" {{ old('marital_status', $member?->marital_status) == 'married' ? 'selected' : '' }}>Married</option>
                                                <option value="widowed" {{ old('marital_status', $member?->marital_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                                <option value="divorced" {{ old('marital_status', $member?->marital_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                            </select>
                                            @error('marital_status')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6" id="wedding_date_group" style="display: none;">
                                        <div class="form-group">
                                            <label for="wedding_date">
                                                <i class="fas fa-ring"></i> Wedding Date
                                            </label>
                                            <input type="date" name="wedding_date" id="wedding_date" 
                                                   class="form-control @error('wedding_date') is-invalid @enderror" 
                                                   value="{{ old('wedding_date', $member?->wedding_date?->format('Y-m-d')) }}" 
                                                   max="{{ date('Y-m-d') }}">
                                            @error('wedding_date')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="form-text text-muted">For anniversary celebrations</small>
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
                                                <i class="fas fa-envelope"></i> Email Address <span class="text-danger">*</span>
                                            </label>
                                            <input type="email" name="email" id="email" 
                                                   class="form-control @error('email') is-invalid @enderror" 
                                                   value="{{ old('email', $user->email) }}" 
                                                   placeholder="your.email@example.com" required>
                                            @error('email')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="form-text text-muted">Used for system notifications and communication</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">
                                                <i class="fas fa-phone"></i> Phone Number
                                            </label>
                                            <input type="text" name="phone" id="phone" 
                                                   class="form-control @error('phone') is-invalid @enderror" 
                                                   value="{{ old('phone', $member?->phone) }}" 
                                                   placeholder="+1234567890">
                                            @error('phone')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="form-text text-muted">Include country code for international numbers</small>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="address">
                                                <i class="fas fa-map-marker-alt"></i> Address
                                            </label>
                                            <textarea name="address" id="address" rows="3" 
                                                      class="form-control @error('address') is-invalid @enderror" 
                                                      placeholder="Street address, city, state, zip code">{{ old('address', $member?->address) }}</textarea>
                                            @error('address')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="form-text text-muted">Your current residential address</small>
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
                                                <i class="fas fa-praying-hands"></i> Salvation Date
                                            </label>
                                            <input type="date" name="salvation_date" id="salvation_date" 
                                                   class="form-control @error('salvation_date') is-invalid @enderror" 
                                                   value="{{ old('salvation_date', $member?->salvation_date?->format('Y-m-d')) }}" 
                                                   max="{{ date('Y-m-d') }}">
                                            @error('salvation_date')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="form-text text-muted">The day you accepted Christ</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="baptism_date">
                                                <i class="fas fa-water"></i> Baptism Date
                                            </label>
                                            <input type="date" name="baptism_date" id="baptism_date" 
                                                   class="form-control @error('baptism_date') is-invalid @enderror" 
                                                   value="{{ old('baptism_date', $member?->baptism_date?->format('Y-m-d')) }}" 
                                                   max="{{ date('Y-m-d') }}">
                                            @error('baptism_date')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="form-text text-muted">The day you were baptized</small>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Note:</strong> These dates help us celebrate your spiritual milestones and provide pastoral care.
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
                                                <i class="fas fa-user-shield"></i> Emergency Contact Name
                                            </label>
                                            <input type="text" name="emergency_contact_name" id="emergency_contact_name" 
                                                   class="form-control @error('emergency_contact_name') is-invalid @enderror" 
                                                   value="{{ old('emergency_contact_name', $member?->emergency_contact_name) }}" 
                                                   placeholder="Full name of emergency contact">
                                            @error('emergency_contact_name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="form-text text-muted">Person to contact in case of emergency</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="emergency_contact_phone">
                                                <i class="fas fa-phone-alt"></i> Emergency Contact Phone
                                            </label>
                                            <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" 
                                                   class="form-control @error('emergency_contact_phone') is-invalid @enderror" 
                                                   value="{{ old('emergency_contact_phone', $member?->emergency_contact_phone) }}" 
                                                   placeholder="+1234567890">
                                            @error('emergency_contact_phone')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="form-text text-muted">This person's phone number</small>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <strong>Important:</strong> Please ensure this contact information is current and accurate.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Profile Photo Tab -->
                            <div class="tab-pane fade" id="photo" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12 text-center mb-3">
                                        @if($member && $member->profile_photo)
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
                                                <i class="fas fa-camera"></i> Upload New Profile Photo
                                            </label>
                                            <div class="custom-file">
                                                <input type="file" name="profile_photo" id="profile_photo" 
                                                       class="custom-file-input @error('profile_photo') is-invalid @enderror" 
                                                       accept="image/*">
                                                <label class="custom-file-label" for="profile_photo">Choose file</label>
                                            </div>
                                            @error('profile_photo')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                            <small class="form-text text-muted">
                                                Recommended: Square image, at least 200x200px. Max 2MB. Formats: JPG, PNG, GIF
                                            </small>
                                        </div>
                                    </div>

                                    <div class="col-md-12 text-center" id="preview-container" style="display: none;">
                                        <h5 class="mb-2">Preview:</h5>
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
                                                           {{ $member && $member->departments->contains($department->id) ? 'checked' : '' }}>
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
                                                No departments are currently available. Please contact the administrator.
                                            </div>
                                        </div>
                                    @endif
                            </div>
                        </div>
                    </div>
                    <!-- Form Actions -->
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <a href="{{ route('profile.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Profile
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
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
    $('#profileForm').submit(function(e) {
        // Basic client-side validation
        let isValid = true;
        let errorMessage = '';

        // Check required fields
        if (!$('#name').val().trim()) {
            isValid = false;
            errorMessage += 'Full name is required.\n';
        }

        if (!$('#email').val().trim()) {
            isValid = false;
            errorMessage += 'Email is required.\n';
        }

        if (!$('#gender').val()) {
            isValid = false;
            errorMessage += 'Gender is required.\n';
        }

        if (!$('#date_of_birth').val()) {
            isValid = false;
            errorMessage += 'Date of birth is required.\n';
        }

        if (!$('#marital_status').val()) {
            isValid = false;
            errorMessage += 'Marital status is required.\n';
        }

        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields:\n\n' + errorMessage);
            return false;
        }

        // Show loading state
        $(this).find('button[type=submit]').prop('disabled', true).html(
            '<i class="fas fa-spinner fa-spin"></i> Saving...'
        );
    });
});
</script>
@endpush
@endsection
