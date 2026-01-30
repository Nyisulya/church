@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">📱 Smart Communication Center</h1>
            <p class="text-muted">Send automated SMS and Emails to your congregation.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="row">
        <!-- Compose Message -->
        <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-pen-fancy mr-2"></i> Compose Message</h3>
                </div>
                <form action="{{ route('reports.communication.send') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label>Recipient Group</label>
                            <select name="recipient_group" id="recipient_group" class="form-control" onchange="toggleSpecificMember()">
                                <option value="all" {{ request('group') == 'all' ? 'selected' : '' }}>All Active Members</option>
                                <option value="leaders" {{ request('group') == 'leaders' ? 'selected' : '' }}>Church Leaders</option>
                                <option value="choir" {{ request('group') == 'choir' ? 'selected' : '' }}>Choir Members</option>
                                <option value="specific" {{ request('group') == 'specific' ? 'selected' : '' }}>Specific Member</option>
                            </select>
                        </div>

                        <div class="form-group" id="specific_member_div" style="display: none;">
                            <label>Select Member</label>
                            <select name="specific_member_id" class="form-control select2">
                                @foreach(App\Models\Member::where('status', 'active')->orderBy('full_name')->get() as $member)
                                    <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Channel</label>
                            <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                <label class="btn btn-outline-primary active">
                                    <input type="radio" name="channel" value="sms" checked onchange="toggleSubject(false)"> 
                                    <i class="fas fa-sms mr-2"></i> SMS
                                </label>
                                <label class="btn btn-outline-primary">
                                    <input type="radio" name="channel" value="email" onchange="toggleSubject(true)"> 
                                    <i class="fas fa-envelope mr-2"></i> Email
                                </label>
                                <label class="btn btn-outline-success">
                                    <input type="radio" name="channel" value="whatsapp" onchange="toggleSubject(false)"> 
                                    <i class="fab fa-whatsapp mr-2"></i> WhatsApp
                                </label>
                            </div>
                        </div>

                        <div class="form-group" id="subject_div" style="display: none;">
                            <label>Subject</label>
                            <input type="text" name="subject" class="form-control" placeholder="Email Subject">
                        </div>

                        <div class="form-group">
                            <label>Message</label>
                            <textarea name="message" class="form-control" rows="5" placeholder="Type your message here..." required></textarea>
                            <small class="text-muted text-right d-block mt-1">Characters: <span id="char_count">0</span></small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" id="sendBtn">
                            <i class="fas fa-paper-plane mr-2"></i> Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Automated Tasks -->
        <div class="col-md-4">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-robot mr-2"></i> Automated Tasks</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>🎂 Birthday Wishes</strong>
                                <p class="text-muted small mb-0">Sends SMS at 8:00 AM</p>
                            </div>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="birthdaySwitch" checked>
                                <label class="custom-control-label" for="birthdaySwitch"></label>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>💰 Pledge Reminders</strong>
                                <p class="text-muted small mb-0">Monthly reminder on 1st</p>
                            </div>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="pledgeSwitch" checked>
                                <label class="custom-control-label" for="pledgeSwitch"></label>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>📅 Event Reminders</strong>
                                <p class="text-muted small mb-0">24 hours before event</p>
                            </div>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="eventSwitch">
                                <label class="custom-control-label" for="eventSwitch"></label>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Recent Logs -->
            <div class="card card-secondary card-outline mt-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-history mr-2"></i> Recent Activity</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>To</th>
                                <th>Type</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>All Members</td>
                                <td><span class="badge badge-primary">SMS</span></td>
                                <td>2m ago</td>
                            </tr>
                            <tr>
                                <td>John Doe</td>
                                <td><span class="badge badge-info">Email</span></td>
                                <td>1h ago</td>
                            </tr>
                            <tr>
                                <td>Choir</td>
                                <td><span class="badge badge-primary">SMS</span></td>
                                <td>Yesterday</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleSpecificMember() {
    var group = document.getElementById('recipient_group').value;
    var div = document.getElementById('specific_member_div');
    div.style.display = group === 'specific' ? 'block' : 'none';
}

function toggleSubject(show) {
    var div = document.getElementById('subject_div');
    div.style.display = show ? 'block' : 'none';
}

document.querySelector('textarea[name="message"]').addEventListener('input', function() {
    document.getElementById('char_count').innerText = this.value.length;
});

document.querySelector('form').addEventListener('submit', function(e) {
    var channel = document.querySelector('input[name="channel"]:checked').value;
    var group = document.getElementById('recipient_group').value;
    
    if (channel === 'whatsapp') {
        e.preventDefault();
        
        if (group !== 'specific') {
            alert('WhatsApp messaging is currently only available for specific members. Please select "Specific Member" as the recipient group.');
            return;
        }
        
        var memberSelect = document.querySelector('select[name="specific_member_id"]');
        var memberId = memberSelect.value;
        var message = document.querySelector('textarea[name="message"]').value;
        
        // We need to get the phone number. Since we don't have it in the DOM, 
        // we'll fetch it via AJAX or just redirect to a route that handles the redirect.
        // For simplicity in this demo, let's assume we redirect to a controller method 
        // that redirects to WhatsApp.
        
        // Actually, let's just submit the form but change the action to a new route
        // or handle it in the existing controller to redirect to WhatsApp.
        
        // Let's modify the form action temporarily
        var form = this;
        var originalAction = form.action;
        
        // Create a hidden input to indicate we want a whatsapp redirect
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'whatsapp_redirect';
        input.value = '1';
        form.appendChild(input);
        
        form.submit();
    }
});
</script>
@endsection
