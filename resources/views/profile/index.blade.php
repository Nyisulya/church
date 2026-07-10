@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">{{ __('My Profile') }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('Profile') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <!-- Success/Warning Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show shadow-sm">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fas fa-exclamation-circle mr-1"></i> {{ session('warning') }}
            </div>
        @endif

        <div class="row">
            <!-- Left Column: Profile Card & QR Code -->
            <div class="col-lg-4 col-md-5">
                <!-- Basic Profile Card -->
                <div class="card card-primary card-outline shadow-sm text-center">
                    <div class="card-body box-profile">
                        <div class="text-center mb-3">
                            @if($member && $member->profile_photo)
                                <img src="{{ asset('storage/' . $member->profile_photo) }}" 
                                     alt="Profile Photo" 
                                     class="profile-user-img img-fluid img-circle shadow"
                                     style="width: 140px; height: 140px; object-fit: cover; border: 3px solid #adb5bd;">
                            @else
                                <div class="profile-user-img img-fluid img-circle bg-primary d-inline-flex align-items-center justify-content-center shadow" 
                                     style="width: 140px; height: 140px; border: 3px solid #adb5bd;">
                                    <i class="fas fa-user fa-4x text-white"></i>
                                </div>
                            @endif
                        </div>
                        <h3 class="profile-username text-center font-weight-bold mb-1">{{ $user->name }}</h3>
                        
                        @if($member)
                            <p class="text-muted text-center mb-2">
                                <i class="fas fa-id-card mr-1"></i> {{ $member->member_number }}
                            </p>
                            <div class="text-center mb-4">
                                <span class="badge badge-{{ $member->status === 'active' ? 'success' : 'secondary' }} px-3 py-2 text-sm text-uppercase">
                                    {{ $member->status }}
                                </span>
                            </div>
                        @endif

                        <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-block font-weight-bold">
                            <i class="fas fa-edit mr-1"></i> {{ __('Edit Profile') }}
                        </a>
                    </div>
                </div>

                @if($member)
                    <!-- QR Code Card -->
                    <div class="card card-primary card-outline shadow-sm mt-3">
                        <div class="card-header text-center py-2 bg-light">
                            <h5 class="card-title m-0 font-weight-bold text-md text-primary">
                                <i class="fas fa-qrcode mr-1"></i> {{ __('My QR Code') }}
                            </h5>
                        </div>
                        <div class="card-body text-center p-3">
                            <div class="d-inline-block p-3 bg-white rounded shadow-sm border mb-2">
                                {!! QrCode::size(150)->generate($member->member_number) !!}
                            </div>
                            <p class="text-xs text-muted mb-3">
                                <i class="fas fa-info-circle mr-1"></i> {{ __('Scan this QR code for church attendance') }}
                            </p>
                            
                            <!-- Download and Print Buttons -->
                            <div class="btn-group w-100" role="group">
                                <a href="{{ route('profile.qr-download') }}" class="btn btn-outline-primary btn-sm w-50">
                                    <i class="fas fa-download mr-1"></i> {{ __('Download') }}
                                </a>
                                <button type="button" class="btn btn-outline-secondary btn-sm w-50" onclick="printQR()">
                                    <i class="fas fa-print mr-1"></i> {{ __('Print') }}
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: Detailed Information -->
            <div class="col-lg-8 col-md-7 mt-3 mt-md-0">
                @if($member)
                    <!-- Contact & Personal Information Card -->
                    <div class="card card-primary card-outline shadow-sm">
                        <div class="card-header bg-light">
                            <h3 class="card-title font-weight-bold text-primary">
                                <i class="fas fa-user-circle mr-1"></i> {{ __('Contact & Personal Information') }}
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped table-hover mb-0">
                                <tbody>
                                    <tr>
                                        <td width="35%"><strong><i class="fas fa-envelope mr-2 text-muted"></i> {{ __('Email') }}</strong></td>
                                        <td>{{ $member->email }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong><i class="fas fa-phone mr-2 text-muted"></i> {{ __('Phone') }}</strong></td>
                                        <td>{{ $member->phone ?? __('Not provided') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong><i class="fas fa-map-marker-alt mr-2 text-muted"></i> {{ __('Address') }}</strong></td>
                                        <td>{{ $member->address ?? __('Not provided') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong><i class="fas fa-venus-mars mr-2 text-muted"></i> {{ __('Gender') }}</strong></td>
                                        <td class="text-capitalize">{{ $member->gender }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong><i class="fas fa-birthday-cake mr-2 text-muted"></i> {{ __('Date of Birth') }}</strong></td>
                                        <td>
                                            {{ $member->date_of_birth ? $member->date_of_birth->format('F d, Y') : __('Not provided') }}
                                            @if($member->date_of_birth)
                                                <span class="badge badge-info ml-2">{{ $member->age }} {{ __('years old') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><i class="fas fa-heart mr-2 text-muted"></i> {{ __('Marital Status') }}</strong></td>
                                        <td class="text-capitalize">{{ $member->marital_status }}</td>
                                    </tr>
                                    @if($member->wedding_date)
                                        <tr>
                                            <td><strong><i class="fas fa-ring mr-2 text-muted"></i> {{ __('Wedding Date') }}</strong></td>
                                            <td>
                                                {{ $member->wedding_date->format('F d, Y') }}
                                                <span class="badge badge-success ml-2">{{ $member->years_married }} {{ __('years married') }}</span>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Spiritual Journey & Emergency Contacts Row -->
                    <div class="row mt-3">
                        <!-- Spiritual Journey Card -->
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="card card-primary card-outline shadow-sm h-100 mb-0">
                                <div class="card-header bg-light py-2">
                                    <h3 class="card-title font-weight-bold text-primary text-sm mb-0">
                                        <i class="fas fa-cross mr-1"></i> {{ __('Spiritual Journey') }}
                                    </h3>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-striped table-hover mb-0">
                                        <tbody>
                                            <tr>
                                                <td width="50%"><strong><i class="fas fa-praying-hands mr-2 text-muted"></i> {{ __('Salvation Date') }}</strong></td>
                                                <td>
                                                    @if($member->salvation_date)
                                                        {{ $member->salvation_date->format('F d, Y') }}
                                                        <br>
                                                        <small class="text-muted">({{ $member->years_since_salvation }} {{ __('years in faith') }})</small>
                                                    @else
                                                        {{ __('Not provided') }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong><i class="fas fa-water mr-2 text-muted"></i> {{ __('Baptism Date') }}</strong></td>
                                                <td>
                                                    {{ $member->baptism_date ? $member->baptism_date->format('F d, Y') : __('Not provided') }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact Card -->
                        <div class="col-md-6">
                            <div class="card card-primary card-outline shadow-sm h-100 mb-0">
                                <div class="card-header bg-light py-2">
                                    <h3 class="card-title font-weight-bold text-primary text-sm mb-0">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> {{ __('Emergency Contact') }}
                                    </h3>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-striped table-hover mb-0">
                                        <tbody>
                                            <tr>
                                                <td width="40%"><strong><i class="fas fa-user mr-2 text-muted"></i> {{ __('Name') }}</strong></td>
                                                <td>{{ $member->emergency_contact_name ?? __('Not provided') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong><i class="fas fa-phone mr-2 text-muted"></i> {{ __('Phone') }}</strong></td>
                                                <td>{{ $member->emergency_contact_phone ?? __('Not provided') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Departments & Ministries Card -->
                    @if($member->departments && $member->departments->count() > 0)
                        <div class="card card-primary card-outline shadow-sm mt-3">
                            <div class="card-header bg-light">
                                <h3 class="card-title font-weight-bold text-primary">
                                    <i class="fas fa-users mr-1"></i> {{ __('Departments & Ministries') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($member->departments as $department)
                                        <div class="col-md-6 col-sm-12 mb-3">
                                            <div class="info-box bg-light border shadow-sm mb-0">
                                                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-church"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text font-weight-bold text-md mb-1">{{ $department->name }}</span>
                                                    <span class="info-box-number text-muted text-sm font-weight-normal">
                                                        Role: <span class="text-capitalize">{{ $department->pivot->role ?? 'member' }}</span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <!-- Warning Card if Profile incomplete -->
                    <div class="alert alert-warning p-4 shadow-sm border-0">
                        <h5 class="font-weight-bold"><i class="icon fas fa-exclamation-triangle mr-1"></i> {{ __('Complete Your Profile') }}</h5>
                        <p class="mb-3">{{ __('Your account is not linked to a member profile yet. Please complete your profile to access all features and get your QR code for attendance.') }}</p>
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary font-weight-bold text-white shadow">
                            <i class="fas fa-user-plus mr-1"></i> {{ __('Complete Your Profile Now') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Footer Card -->
        <div class="card shadow-sm mt-3">
            <div class="card-footer bg-white">
                <div class="row">
                    <div class="col-sm-6 text-center text-sm-left">
                        <small class="text-muted">
                            <i class="fas fa-clock mr-1"></i>
                            Member since: {{ $user->created_at->format('F Y') }}
                        </small>
                    </div>
                    <div class="col-sm-6 text-center text-sm-right mt-2 mt-sm-0">
                        @if(auth()->user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'treasurer', 'department_leader']))
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm shadow-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function printQR() {
    // Create a new window
    const printWindow = window.open('', '', 'height=600,width=800');
    
    // Get QR code SVG
    const qrCodeElement = document.querySelector('.card-body svg');
    const memberNumber = '{{ $member->member_number ?? '' }}';
    const memberName = '{{ $user->name ?? '' }}';
    
    if(!qrCodeElement) {
        alert('QR code not found');
        return;
    }
    
    // Write content to print window
    printWindow.document.write('<html><head><title>My QR Code</title>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }');
    printWindow.document.write('h2 { margin-bottom: 10px; }');
    printWindow.document.write('p { color: #666; margin: 5px 0; }');
    printWindow.document.write('.qr-container { margin: 20px auto; }');
    printWindow.document.write('</style></head><body>');
    printWindow.document.write('<h2>' + memberName + '</h2>');
    printWindow.document.write('<p>Member #: ' + memberNumber + '</p>');
    printWindow.document.write('<div class="qr-container">');
    printWindow.document.write(qrCodeElement.outerHTML);
    printWindow.document.write('</div>');
    printWindow.document.write('<p>Scan this code for attendance</p>');
    printWindow.document.write('</body></html>');
    
    printWindow.document.close();
    printWindow.focus();
    
    // Trigger print after content is loaded
    setTimeout(function() {
        printWindow.print();
        printWindow.close();
    }, 250);
}
</script>
@endpush
@endsection
