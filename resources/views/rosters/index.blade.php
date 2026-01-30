@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-md-8">
            <h1 class="m-0 text-dark">📋 Volunteer Rostering</h1>
        </div>
        <div class="col-md-4 text-right">
            <button class="btn btn-success" data-toggle="modal" data-target="#manageRolesModal">
                <i class="fas fa-cog"></i> Manage Roles
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Upcoming Events & Schedules</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Event</th>
                                <th>Volunteers</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($events as $event)
                            <tr>
                                <td>{{ $event->date->format('M d, Y H:i') }}</td>
                                <td>{{ $event->name }}</td>
                                <td>
                                    @foreach($event->rosters as $roster)
                                        <span class="badge badge-info">{{ $roster->member->full_name }} ({{ $roster->role }})</span>
                                        <form action="{{ route('rosters.destroy', $roster) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs text-danger" onclick="return confirm('Remove?')">&times;</button>
                                        </form>
                                        <br>
                                    @endforeach
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#assignModal{{ $event->id }}">
                                        <i class="fas fa-plus"></i> Assign
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="assignModal{{ $event->id }}" tabindex="-1" role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Assign Volunteer to {{ $event->name }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form action="{{ route('rosters.store') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="event_id" value="{{ $event->id }}">
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>Member</label>
                                                            <select name="member_id" class="form-control select2" style="width: 100%;" required>
                                                                <option value="">Select Member</option>
                                                                @foreach(App\Models\Member::where('status', 'active')->orderBy('full_name')->get() as $member)
                                                                    <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Role</label>
                                                            <select name="role" class="form-control" required>
                                                                <option value="Usher">Usher</option>
                                                                <option value="Worship Team">Worship Team</option>
                                                                <option value="Media">Media</option>
                                                                <option value="Greeter">Greeter</option>
                                                                <option value="Sunday School">Sunday School</option>
                                                                <option value="Security">Security</option>
                                                                <option value="Parking">Parking</option>
                                                                <option value="Sound">Sound</option>
                                                                <option value="Prayer Team">Prayer Team</option>
                                                                <option value="Hospitality">Hospitality</option>
                                                                <option value="Cleaning">Cleaning</option>
                                                                <option value="Choir">Choir</option>
                                                                <option value="Nursery">Nursery</option>
                                                                <option value="Registration">Registration</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary">Assign</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No upcoming events found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    {{ $events->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Manage Roles Modal -->
    <div class="modal fade" id="manageRolesModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white"><i class="fas fa-cog"></i> Manage Volunteer Roles</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Add New Role -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-plus"></i> Add New Role</h6>
                        </div>
                        <div class="card-body">
                            <form id="addRoleForm" class="form-inline">
                                <input type="text" id="newRoleName" class="form-control mr-2" placeholder="Enter role name (e.g., Translator)" required>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Role
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Current Roles List -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-list"></i> Current Roles</h6>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0" id="rolesTable">
                                <thead>
                                    <tr>
                                        <th>Role Name</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="rolesList">
                                    <tr data-role="Usher">
                                        <td><span class="role-name">Usher</span></td>
                                        <td class="text-right">
                                            <button class="btn btn-sm btn-warning edit-role" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-role" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr data-role="Worship Team">
                                        <td><span class="role-name">Worship Team</span></td>
                                        <td class="text-right">
                                            <button class="btn btn-sm btn-warning edit-role" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-role" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr data-role="Media">
                                        <td><span class="role-name">Media</span></td>
                                        <td class="text-right">
                                            <button class="btn btn-sm btn-warning edit-role" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-role" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr data-role="Greeter">
                                        <td><span class="role-name">Greeter</span></td>
                                        <td class="text-right">
                                            <button class="btn btn-sm btn-warning edit-role" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-role" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr data-role="Sunday School">
                                        <td><span class="role-name">Sunday School</span></td>
                                        <td class="text-right">
                                            <button class="btn btn-sm btn-warning edit-role" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-role" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr data-role="Security">
                                        <td><span class="role-name">Security</span></td>
                                        <td class="text-right">
                                            <button class="btn btn-sm btn-warning edit-role" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-role" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr data-role="Parking">
                                        <td><span class="role-name">Parking</span></td>
                                        <td class="text-right">
                                            <button class="btn btn-sm btn-warning edit-role" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-role" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr data-role="Sound">
                                        <td><span class="role-name">Sound</span></td>
                                        <td class="text-right">
                                            <button class="btn btn-sm btn-warning edit-role" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-role" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr data-role="Prayer Team">
                                        <td><span class="role-name">Prayer Team</span></td>
                                        <td class="text-right">
                                            <button class="btn btn-sm btn-warning edit-role" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-role" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr data-role="Hospitality">
                                        <td><span class="role-name">Hospitality</span></td>
                                        <td class="text-right">
                                            <button class="btn btn-sm btn-warning edit-role" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-role" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr data-role="Cleaning">
                                        <td><span class="role-name">Cleaning</span></td>
                                        <td class="text-right">
                                            <button class="btn btn-sm btn-warning edit-role" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-role" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr data-role="Choir">
                                        <td><span class="role-name">Choir</span></td>
                                        <td class="text-right">
                                            <button class="btn btn-sm btn-warning edit-role" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-role" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr data-role="Nursery">
                                        <td><span class="role-name">Nursery</span></td>
                                        <td class="text-right">
                                            <button class="btn btn-sm btn-warning edit-role" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-role" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr data-role="Registration">
                                        <td><span class="role-name">Registration</span></td>
                                        <td class="text-right">
                                            <button class="btn btn-sm btn-warning edit-role" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-role" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Add Role
    document.getElementById('addRoleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const roleName = document.getElementById('newRoleName').value.trim();
        
        if (roleName) {
            // Add to table
            const tbody = document.getElementById('rolesList');
            const newRow = document.createElement('tr');
            newRow.setAttribute('data-role', roleName);
            newRow.innerHTML = `
                <td><span class="role-name">${roleName}</span></td>
                <td class="text-right">
                    <button class="btn btn-sm btn-warning edit-role" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-role" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(newRow);
            
            // Add to assignment dropdown
            document.querySelectorAll('select[name="role"]').forEach(select => {
                const option = document.createElement('option');
                option.value = roleName;
                option.textContent = roleName;
                select.appendChild(option);
            });
            
            document.getElementById('newRoleName').value = '';
            alert('Role added successfully!');
        }
    });

    // Edit Role
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-role')) {
            const row = e.target.closest('tr');
            const roleNameSpan = row.querySelector('.role-name');
            const oldName = roleNameSpan.textContent;
            const newName = prompt('Edit role name:', oldName);
            
            if (newName && newName.trim() && newName !== oldName) {
                // Update in table
                roleNameSpan.textContent = newName;
                row.setAttribute('data-role', newName);
                
                // Update in all assignment dropdowns
                document.querySelectorAll('select[name="role"] option').forEach(option => {
                    if (option.value === oldName) {
                        option.value = newName;
                        option.textContent = newName;
                    }
                });
                
                alert('Role updated successfully!');
            }
        }
    });

    // Delete Role
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-role')) {
            if (confirm('Are you sure you want to delete this role?')) {
                const row = e.target.closest('tr');
                const roleName = row.getAttribute('data-role');
                
                // Remove from table
                row.remove();
                
                // Remove from all assignment dropdowns
                document.querySelectorAll('select[name="role"] option').forEach(option => {
                    if (option.value === roleName) {
                        option.remove();
                    }
                });
                
                alert('Role deleted successfully!');
            }
        }
    });
    </script>
</div>
@endsection
