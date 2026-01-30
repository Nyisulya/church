@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mt-4">
        <div class="col-md-10">
            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user"></i> My Profile
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> Edit Profile
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Left Column - Basic Info -->
                        <div class="col-md-4 text-center">
                            <div class="mb-3">
                                @if($member && $member->profile_photo)
                                    <img src="{{ asset('storage/' . $member->profile_photo) }}" 
                                         alt="Profile Photo" 
                                         class="img-circle img-fluid"
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                @else
                                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" 
                                         style="width: 150px; height: 150px;">
                                        <i class="fas fa-user fa-4x text-white"></i>
                                    </div>
                                @endif
                            </div>
                            <h4 class="mb-1">{{ $user->name }}</h4>
                            @if($member)
                                <p class="text-muted">
                                    <i class="fas fa-id-card"></i> {{ $member->member_number }}
                                </p>
                                <span class="badge badge-{{ $member->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($member->status) }}
                                </span>
                                
                                <!-- QR Code Card -->
                                <div class="card card-primary card-outline mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-qrcode"></i> My QR Code
                                        </h5>
                                    </div>
                                    <div class="card-body text-center p-3">
                                        {!! QrCode::size(180)->generate($member->member_number) !!}
                                        <p class="text-sm text-muted mt-2 mb-2">
                                            <i class="fas fa-info-circle"></i> For attendance scanning
                                        </p>
                                        
                                        <!-- Download and Print Buttons -->
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('profile.qr-download') }}" 
                                               class="btn btn-primary" 
                                               title="Download QR Code">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-secondary" 
                                                    onclick="printQR()" 
                                                    title="Print QR Code">
                                                <i class="fas fa-print"></i> Print
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Right Column - Detailed Info -->
                        <div class="col-md-8">
                            @if($member)
                                <!-- Contact Information -->
                                <div class="mb-4">
                                    <h5 class="border-bottom pb-2"><i class="fas fa-address-card"></i> Contact Information</h5>
                                    <table class="table table-sm table-hover">
                                        <tbody>
                                            <tr>
                                                <td><strong><i class="fas fa-envelope"></i> Email:</strong></td>
                                                <td>{{ $member->email }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong><i class="fas fa-phone"></i> Phone:</strong></td>
                                                <td>{{ $member->phone ?? 'Not provided' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong><i class="fas fa-map-marker-alt"></i> Address:</strong></td>
                                                <td>{{ $member->address ?? 'Not provided' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Personal Information -->
                                <div class="mb-4">
                                    <h5 class="border-bottom pb-2"><i class="fas fa-user-circle"></i> Personal Information</h5>
                                    <table class="table table-sm table-hover">
                                        <tbody>
                                            <tr>
                                                <td><strong><i class="fas fa-venus-mars"></i> Gender:</strong></td>
                                                <td>{{ ucfirst($member->gender) }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong><i class="fas fa-birthday-cake"></i> Date of Birth:</strong></td>
                                                <td>
                                                    {{ $member->date_of_birth ? $member->date_of_birth->format('F d, Y') : 'Not provided' }}
                                                    @if($member->date_of_birth)
                                                        ({{ $member->age }} years old)
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong><i class="fas fa-heart"></i> Marital Status:</strong></td>
                                                <td>{{ ucfirst($member->marital_status) }}</td>
                                            </tr>
                                            @if($member->wedding_date)
                                                <tr>
                                                    <td><strong><i class="fas fa-ring"></i> Wedding Date:</strong></td>
                                                    <td>
                                                        {{ $member->wedding_date->format('F d, Y') }}
                                                        ({{ $member->years_married }} years)
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Spiritual Information -->
                                <div class="mb-4">
                                    <h5 class="border-bottom pb-2"><i class="fas fa-cross"></i> Spiritual Journey</h5>
                                    <table class="table table-sm table-hover">
                                        <tbody>
                                            @if($member->salvation_date)
                                                <tr>
                                                    <td><strong><i class="fas fa-praying-hands"></i> Salvation Date:</strong></td>
                                                    <td>
                                                        {{ $member->salvation_date->format('F d, Y') }}
                                                        ({{ $member->years_since_salvation }} years)
                                                    </td>
                                                </tr>
                                            @endif
                                            @if($member->baptism_date)
                                                <tr>
                                                    <td><strong><i class="fas fa-water"></i> Baptism Date:</strong></td>
                                                    <td>{{ $member->baptism_date->format('F d, Y') }}</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Emergency Contact -->
                                @if($member->emergency_contact_name || $member->emergency_contact_phone)
                                    <div class="mb-4">
                                        <h5 class="border-bottom pb-2"><i class="fas fa-exclamation-triangle"></i> Emergency Contact</h5>
                                        <table class="table table-sm table-hover">
                                            <tbody>
                                                @if($member->emergency_contact_name)
                                                    <tr>
                                                        <td><strong><i class="fas fa-user"></i> Name:</strong></td>
                                                        <td>{{ $member->emergency_contact_name }}</td>
                                                    </tr>
                                                @endif
                                                @if($member->emergency_contact_phone)
                                                    <tr>
                                                        <td><strong><i class="fas fa-phone"></i> Phone:</strong></td>
                                                        <td>{{ $member->emergency_contact_phone }}</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                @endif

                                <!-- Departments -->
                                @if($member->departments && $member->departments->count() > 0)
                                    <div class="mb-4">
                                        <h5 class="border-bottom pb-2"><i class="fas fa-users"></i> Departments & Ministries</h5>
                                        <div class="row">
                                            @foreach($member->departments as $department)
                                                <div class="col-md-6 mb-2">
                                                    <div class="small-box bg-info">
                                                        <div class="inner">
                                                            <h5>{{ $department->name }}</h5>
                                                            <p>Role: {{ ucfirst($department->pivot->role ?? 'member') }}</p>
                                                        </div>
                                                        <div class="icon">
                                                            <i class="fas fa-church"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-warning">
                                    <h5><i class="fas fa-exclamation-triangle"></i> Complete Your Profile</h5>
                                    <p class="mb-3">Your account is not linked to a member profile yet. Please complete your profile to access all features and get your QR code for attendance.</p>
                                    <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                                        <i class="fas fa-user-plus"></i> Complete Your Profile Now
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="fas fa-clock"></i>
                                Member since: {{ $user->created_at->format('F Y') }}
                            </small>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
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
