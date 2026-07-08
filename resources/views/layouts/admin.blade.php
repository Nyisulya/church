<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manzese SDA Church - Management System</title>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- TailwindCSS (for compatibility with existing views) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    @livewireStyles
    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      
      <!-- Language Switcher -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="fas fa-globe"></i>
          <span class="d-none d-md-inline ml-1">{{ app()->getLocale() == 'sw' ? 'Kiswahili' : 'English' }}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
          <a href="{{ route('lang.switch', 'en') }}" class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}">
            🇺🇸 English
          </a>
          <a href="{{ route('lang.switch', 'sw') }}" class="dropdown-item {{ app()->getLocale() == 'sw' ? 'active' : '' }}">
            🇹🇿 Kiswahili
          </a>
        </div>
      </li>

      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          @if(Auth::user()->unreadNotifications->count() > 0)
            <span class="badge badge-warning navbar-badge">{{ Auth::user()->unreadNotifications->count() }}</span>
          @endif
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">{{ Auth::user()->unreadNotifications->count() }} Notifications</span>
          <div class="dropdown-divider"></div>
          
          @foreach(Auth::user()->unreadNotifications->take(5) as $notification)
            <a href="{{ isset($notification->data['url']) ? url($notification->data['url']) : '#' }}" class="dropdown-item">
              <i class="{{ $notification->data['icon'] ?? 'fas fa-envelope' }} mr-2"></i> {{ Str::limit($notification->data['title'] ?? 'Notification', 20) }}
              <span class="float-right text-muted text-sm">{{ $notification->created_at->diffForHumans() }}</span>
            </a>
            <div class="dropdown-divider"></div>
          @endforeach

          <a href="{{ route('inbox.index') }}" class="dropdown-item dropdown-footer">{{ __('See All Notifications') }}</a>
        </div>
      </li>

      <!-- User Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-user"></i>
          <span class="d-none d-md-inline ml-2">{{ Auth::user()->name ?? 'Guest' }}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">{{ __('Account Settings') }}</span>
          <div class="dropdown-divider"></div>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dropdown-item">
              <i class="fas fa-sign-out-alt mr-2"></i> {{ __('Logout') }}
            </button>
          </form>
        </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Header -->
    <div class="brand-link text-center" style="background:#1e3a8a; padding: 15px 10px;">
      <span class="brand-text font-weight-bold d-block" style="color:white; font-size:14px; line-height: 1.4;">MANZESE SDA<br>CHURCH</span>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

          {{-- Personal — collapsible dropdown --}}
          <li class="nav-item has-treeview {{ request()->routeIs('dashboard') || request()->routeIs('profile.*') || request()->routeIs('inbox.*') || request()->routeIs('attendance.*') || request()->routeIs('rosters.my') || request()->routeIs('care-requests.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->routeIs('dashboard') || request()->routeIs('profile.*') || request()->routeIs('inbox.*') || request()->routeIs('attendance.*') || request()->routeIs('rosters.my') || request()->routeIs('care-requests.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-circle"></i>
              <p>
                {{ __('Personal') }}
                <i class="right fas fa-angle-left"></i>
                @if((isset($inboxUnreadCount) && $inboxUnreadCount > 0) || (Auth::user()->member && !Auth::user()->member->isProfileComplete()))
                  <span class="badge badge-warning right">!</span>
                @endif
              </p>
            </a>
            <ul class="nav nav-treeview">

              {{-- Dashboard --}}
              @if(Auth::user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'treasurer', 'department_leader']))
              <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('Dashboard') }}</p>
                </a>
              </li>
              @endif

              {{-- My Profile --}}
              <li class="nav-item">
                <a href="{{ route('profile.index') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('My Profile') }}
                    @if(Auth::user()->member && !Auth::user()->member->isProfileComplete())
                      <span class="badge badge-warning right">{{ __('Incomplete') }}</span>
                    @endif
                  </p>
                </a>
              </li>

              {{-- My Messages --}}
              <li class="nav-item">
                <a href="{{ route('inbox.index') }}" class="nav-link {{ request()->routeIs('inbox.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('My Messages') }}
                    @if(isset($inboxUnreadCount) && $inboxUnreadCount > 0)
                      <span class="badge badge-danger right">{{ $inboxUnreadCount }}</span>
                    @endif
                  </p>
                </a>
              </li>

              {{-- My Attendance (members only) --}}
              @if(!Auth::user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'treasurer', 'department_leader']))
              <li class="nav-item">
                <a href="{{ route('attendance.index') }}" class="nav-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('My Attendance') }}</p>
                </a>
              </li>
              @endif

              {{-- My Roster --}}
              <li class="nav-item">
                <a href="{{ route('rosters.my') }}" class="nav-link {{ request()->routeIs('rosters.my') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('My Roster') }}
                    @if(isset($myRosterCount) && $myRosterCount > 0)
                      <span class="badge badge-primary right">{{ $myRosterCount }}</span>
                    @endif
                  </p>
                </a>
              </li>

              {{-- Care Requests:
                   - Viongozi (admin/pastor/dept_leader): dropdown na sub-items
                   - Members: link moja ya moja kwa moja --}}
              @if(Auth::user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'department_leader']))
                {{-- Leaders see dropdown with My Requests + Leader Dashboard --}}
                <li class="nav-item has-treeview {{ request()->routeIs('care-requests.*') ? 'menu-open' : '' }}">
                  <a href="#" class="nav-link {{ request()->routeIs('care-requests.*') ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>
                      {{ __('Care Requests') }}
                      <i class="right fas fa-angle-left"></i>
                      @if(isset($pendingCareRequestCount) && $pendingCareRequestCount > 0)
                        <span class="badge badge-warning right">{{ $pendingCareRequestCount }}</span>
                      @endif
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="{{ route('care-requests.index') }}" class="nav-link {{ request()->routeIs('care-requests.index') ? 'active' : '' }}">
                        <i class="far fa-dot-circle nav-icon"></i>
                        <p>{{ __('My Requests') }}</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{ route('care-requests.leader-dashboard') }}" class="nav-link {{ request()->routeIs('care-requests.leader-dashboard') || (request()->routeIs('care-requests.show') && Auth::user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'department_leader'])) ? 'active' : '' }}">
                        <i class="far fa-dot-circle nav-icon"></i>
                        <p>{{ __('Leader Dashboard') }}
                          @if(isset($pendingCareRequestCount) && $pendingCareRequestCount > 0)
                            <span class="badge badge-warning right">{{ $pendingCareRequestCount }}</span>
                          @endif
                        </p>
                      </a>
                    </li>
                  </ul>
                </li>
              @else
                {{-- Members see a single direct link --}}
                <li class="nav-item">
                  <a href="{{ route('care-requests.index') }}" class="nav-link {{ request()->routeIs('care-requests.*') ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ __('Care Requests') }}</p>
                  </a>
                </li>
              @endif

            </ul>
          </li>


          {{-- ══════════════════════════════════ --}}
          {{-- SECTION: CHURCH LIFE / KANISA     --}}
          {{-- ══════════════════════════════════ --}}
          <li class="nav-header" style="letter-spacing:0.08em;font-size:0.68rem;color:rgba(255,255,255,0.45);padding:12px 15px 4px;text-transform:uppercase;">{{ __('Church Life') }}</li>

          {{-- Announcements --}}
          <li class="nav-item">
            @if(Auth::user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'department_leader']))
            <a href="{{ route('announcements.index') }}" class="nav-link {{ request()->routeIs('announcements.*') ? 'active' : '' }}">
            @else
            <a href="{{ route('announcements.member-view') }}" class="nav-link {{ request()->routeIs('announcements.*') ? 'active' : '' }}">
            @endif
              <i class="nav-icon fas fa-bullhorn"></i>
              <p>{{ __('Announcements') }}
                @if(isset($announcementCount) && $announcementCount > 0)
                  <span class="badge badge-warning right">{{ $announcementCount }}</span>
                @endif
              </p>
            </a>
          </li>

          {{-- Events & Calendar --}}
          <li class="nav-item has-treeview {{ request()->routeIs('events.*') || request()->routeIs('reports.calendar') || request()->routeIs('birthdays.*') || request()->routeIs('anniversaries.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->routeIs('events.*') || request()->routeIs('reports.calendar') || request()->routeIs('birthdays.*') || request()->routeIs('anniversaries.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-calendar-alt"></i>
              <p>
                {{ __('Events & Calendar') }}
                <i class="right fas fa-angle-left"></i>
                @if(isset($birthdayCount) && $birthdayCount > 0)
                  <span class="badge badge-info right">{{ $birthdayCount }}</span>
                @endif
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('events.index') }}" class="nav-link {{ request()->routeIs('events.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('Events') }}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('reports.calendar') }}" class="nav-link {{ request()->routeIs('reports.calendar') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('Calendar') }}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('birthdays.index') }}" class="nav-link {{ request()->routeIs('birthdays.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('Birthdays') }}
                    @if(isset($birthdayCount) && $birthdayCount > 0)
                      <span class="badge badge-info right">{{ $birthdayCount }}</span>
                    @endif
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('anniversaries.index') }}" class="nav-link {{ request()->routeIs('anniversaries.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('Anniversaries') }}</p>
                </a>
              </li>
            </ul>
          </li>

          {{-- Ministries --}}
          <li class="nav-item">
            <a href="{{ route('departments.index') }}" class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-church"></i>
              <p>{{ __('Ministries') }}
                @if(isset($ministryNotificationCount) && $ministryNotificationCount > 0)
                  <span class="badge badge-warning right">{{ $ministryNotificationCount }}</span>
                @endif
              </p>
            </a>
          </li>

          {{-- Small Groups --}}
          <li class="nav-item has-treeview {{ request()->routeIs('small-groups.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->routeIs('small-groups.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-friends"></i>
              <p>
                {{ __('Small Groups') }}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('small-groups.my-group') }}" class="nav-link {{ request()->routeIs('small-groups.my-group') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('My Small Group') }}</p>
                </a>
              </li>
              @if(Auth::user()->hasAnyRole(['super_admin', 'admin', 'pastor']))
              <li class="nav-item">
                <a href="{{ route('small-groups.index') }}" class="nav-link {{ request()->routeIs('small-groups.index') || request()->routeIs('small-groups.show') || request()->routeIs('small-groups.create') || request()->routeIs('small-groups.edit') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('Manage Groups') }}</p>
                </a>
              </li>
              @endif
            </ul>
          </li>


          {{-- Giving & Finance — single dropdown --}}
          <li class="nav-item has-treeview {{ request()->routeIs('give.*') || request()->routeIs('contributions.*') || request()->routeIs('pledges.*') || request()->routeIs('ministry-pledges.*') || request()->routeIs('projects.*') || request()->routeIs('financial.*') || request()->routeIs('giving-categories.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->routeIs('give.*') || request()->routeIs('contributions.*') || request()->routeIs('pledges.*') || request()->routeIs('ministry-pledges.*') || request()->routeIs('projects.*') || request()->routeIs('financial.*') || request()->routeIs('giving-categories.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-hand-holding-usd"></i>
              <p>
                {{ __('Giving & Finance') }}
                <i class="right fas fa-angle-left"></i>
                @if(isset($newProjectCount) && $newProjectCount > 0)
                  <span class="badge badge-success right">{{ $newProjectCount }}</span>
                @endif
              </p>
            </a>
            <ul class="nav nav-treeview">
              {{-- Available to all members --}}
              <li class="nav-item">
                <a href="{{ route('give.form') }}" class="nav-link {{ request()->routeIs('give.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon text-success"></i>
                  <p>{{ __('Give Online') }}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('contributions.index') }}" class="nav-link {{ request()->routeIs('contributions.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('My Contributions') }}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('pledges.index') }}" class="nav-link {{ request()->routeIs('pledges.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('My Pledges (Ahadi)') }}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('ministry-pledges.index') }}" class="nav-link {{ request()->routeIs('ministry-pledges.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('Ministry Pledges') }}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('projects.index') }}" class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('Projects') }}
                    @if(isset($newProjectCount) && $newProjectCount > 0)
                      <span class="badge badge-success right">{{ $newProjectCount }}</span>
                    @endif
                  </p>
                </a>
              </li>
              {{-- Admin/Treasurer only --}}
              @if(Auth::user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'treasurer']))
              <li class="nav-item">
                <a href="{{ route('financial.dashboard') }}" class="nav-link {{ request()->routeIs('financial.dashboard') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('Financial Dashboard') }}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('financial.transactions') }}" class="nav-link {{ request()->routeIs('financial.transactions') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('All Transactions') }}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('financial.income.create') }}" class="nav-link {{ request()->routeIs('financial.income.create') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon text-success"></i>
                  <p>{{ __('Record Income') }}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('financial.expense.create') }}" class="nav-link {{ request()->routeIs('financial.expense.create') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon text-danger"></i>
                  <p>{{ __('Record Expense') }}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('giving-categories.index') }}" class="nav-link {{ request()->routeIs('giving-categories.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('Manage Categories') }}</p>
                </a>
              </li>
              @endif
            </ul>
          </li>


          {{-- ════════════════════════════════════════ --}}
          {{-- SECTION: LEADERSHIP / UONGOZI          --}}
          {{-- ════════════════════════════════════════ --}}
          @if(Auth::user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'treasurer', 'department_leader']))
          <li class="nav-header" style="letter-spacing:0.08em;font-size:0.68rem;color:rgba(255,255,255,0.45);padding:12px 15px 4px;text-transform:uppercase;">{{ __('Leadership') }}</li>

          {{-- Members & Visitors --}}
          @can('viewAny', App\Models\Member::class)
          <li class="nav-item">
            <a href="{{ route('members.index') }}" class="nav-link {{ request()->routeIs('members.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-users"></i>
              <p>{{ __('Members') }}</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('visitors.index') }}" class="nav-link {{ request()->routeIs('visitors.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-plus"></i>
              <p>{{ __('Visitors') }}</p>
            </a>
          </li>
          @endcan

          {{-- Attendance (Admin) --}}
          @if(Auth::user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'department_leader']))
          <li class="nav-item has-treeview {{ request()->routeIs('attendance.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-qrcode"></i>
              <p>
                {{ __('Attendance') }}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('attendance.scanner') }}" class="nav-link {{ request()->routeIs('attendance.scanner') || request()->routeIs('attendance.scan') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('QR Scanner') }}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('attendance.index') }}" class="nav-link {{ request()->routeIs('attendance.index') || request()->routeIs('attendance.show') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('Records') }}</p>
                </a>
              </li>
            </ul>
          </li>
          @endif

          {{-- Pastoral Care --}}
          @if(Auth::user()->hasAnyRole(['super_admin', 'admin', 'pastor']))
          <li class="nav-item">
            <a href="{{ route('pastoral-care.dashboard') }}" class="nav-link {{ request()->routeIs('pastoral-care.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-heart"></i>
              <p>{{ __('Pastoral Care') }}</p>
            </a>
          </li>
          @endif

          {{-- Communication --}}
          @if(Auth::user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'department_leader']))
          <li class="nav-item">
            <a href="{{ route('reports.communication.index') }}" class="nav-link {{ request()->routeIs('reports.communication.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-comments"></i>
              <p>{{ __('Communication') }}</p>
            </a>
          </li>
          @endif

          {{-- Assets & Inventory --}}
          @if(Auth::user()->hasAnyRole(['super_admin', 'admin', 'pastor', 'treasurer']))
          <li class="nav-item">
            <a href="{{ route('assets.index') }}" class="nav-link {{ request()->routeIs('assets.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-boxes"></i>
              <p>{{ __('Assets & Inventory') }}</p>
            </a>
          </li>

          {{-- Volunteer Rostering --}}
          <li class="nav-item">
            <a href="{{ route('rosters.index') }}" class="nav-link {{ request()->routeIs('rosters.index') ? 'active' : '' }}">
              <i class="nav-icon fas fa-clipboard-list"></i>
              <p>{{ __('Volunteer Rostering') }}</p>
            </a>
          </li>
          @endif

          {{-- Leaders --}}
          @if(Auth::user()->hasAnyRole(['super_admin', 'admin', 'pastor']))
          <li class="nav-item">
            <a href="{{ route('leaders.index') }}" class="nav-link {{ request()->routeIs('leaders.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-tie"></i>
              <p>{{ __('Leaders') }}</p>
            </a>
          </li>
          @endif

          @endif {{-- end leadership section guard --}}

          {{-- ══════════════════════════════════ --}}
          {{-- SECTION: SYSTEM (Admin only)      --}}
          {{-- ══════════════════════════════════ --}}
          @if(Auth::user()->hasAnyRole(['super_admin', 'admin']))
          <li class="nav-header" style="letter-spacing:0.08em;font-size:0.68rem;color:rgba(255,255,255,0.45);padding:12px 15px 4px;text-transform:uppercase;">{{ __('System') }}</li>

          @can('viewAny', App\Models\User::class)
          <li class="nav-item">
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-cog"></i>
              <p>{{ __('Users') }}</p>
            </a>
          </li>
          @endcan

          <li class="nav-item">
            <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-cog"></i>
              <p>{{ __('System Settings') }}</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-shield"></i>
              <p>{{ __('Roles & Permissions') }}</p>
            </a>
          </li>
          @endif

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Main content -->
    <div class="content pt-3">
      <div class="container-fluid">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        
        @yield('content')
        {{ $slot ?? '' }}
      </div>
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <div class="float-right d-none d-sm-inline">
      Version 1.0
    </div>
    <strong>Copyright &copy; {{ date('Y') }} Manzese Seventh Day Adventist Church.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

@livewireScripts
{{-- Birthday Celebration Modal --}}
@if(auth()->user() && auth()->user()->member && auth()->user()->member->is_birthday_today)
<div id="birthdayModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-2xl p-8 max-w-md w-full text-center transform scale-0 transition-transform duration-500" id="birthdayCard">
        <div class="mb-4">
            <i class="fas fa-birthday-cake text-6xl text-pink-500 animate-bounce"></i>
        </div>
        <h2 class="text-3xl font-bold text-gray-800 mb-2">{{ __('Happy Birthday! 🎉') }}</h2>
        <p class="text-xl text-gray-600 mb-6">
            {{ __('Dear') }} <span class="font-semibold text-pink-600">{{ auth()->user()->member->first_name }}</span>,
        </p>
        <p class="text-gray-600 mb-8 italic">
            "{{ __('The LORD bless you and keep you; the LORD make his face shine on you and be gracious to you.') }}"
            <br><span class="text-sm text-gray-500">- {{ __('Numbers 6:24-25') }}</span>
        </p>
        <p class="text-gray-700 mb-8">
            {{ __('Wishing you a blessed day filled with joy and God\'s love!') }}
            <br>- {{ __('Your Church Family') }}
        </p>

        {{-- Member Wishes --}}
        @php
            $wishes = auth()->user()->notifications()
                ->where('type', 'App\Notifications\BirthdayGreetingNotification')
                ->whereDate('created_at', today())
                ->get();
        @endphp

        @if($wishes->count() > 0)
        <button onclick="toggleWishes()" id="viewWishesBtn" class="text-pink-600 font-semibold underline mb-6 hover:text-pink-800 transition block mx-auto">
            💌 {{ __('View') }} {{ $wishes->count() }} {{ __('Wishes from Friends') }}
        </button>
        
        <div id="wishesList" class="hidden bg-pink-50 rounded-lg p-4 mb-6 text-left max-h-40 overflow-y-auto">
            <h3 class="text-sm font-bold text-pink-600 mb-2">💌 {{ __('Wishes from Friends') }}:</h3>
            <ul class="space-y-2">
                @foreach($wishes as $wish)
                <li class="text-sm text-gray-700 border-b border-pink-100 pb-1 last:border-0">
                    <span class="font-semibold">{{ $wish->data['sender'] ?? 'A Friend' }}:</span>
                    "{{ __($wish->data['message'] ?? 'Happy Birthday!') }}"
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        <button onclick="closeBirthdayModal()" class="bg-gradient-to-r from-pink-500 to-purple-600 text-white px-6 py-2 rounded-full font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 transition">
            {{ __('Thank You! ❤️') }}
        </button>
    </div>
</div>

@endif

{{-- Anniversary Modal --}}
@if(auth()->user() && auth()->user()->member && auth()->user()->member->is_anniversary_today)
<div id="anniversaryModal" class="fixed inset-0 flex items-center justify-center z-50 hidden bg-black bg-opacity-50 backdrop-blur-sm transition-opacity duration-300">
    <div id="anniversaryCard" class="bg-white rounded-2xl shadow-2xl p-8 max-w-lg w-full text-center transform scale-0 transition-transform duration-500 relative overflow-hidden border-4 border-red-200">
        <!-- Decorative Elements -->
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-red-400 via-pink-500 to-red-400"></div>
        <div class="absolute top-4 right-4 text-red-200 opacity-50"><i class="fas fa-heart text-6xl"></i></div>
        <div class="absolute bottom-4 left-4 text-red-200 opacity-50"><i class="fas fa-ring text-6xl"></i></div>

        <div class="mb-4">
            <i class="fas fa-heart text-6xl text-red-500 animate-pulse"></i>
        </div>
        <h2 class="text-3xl font-bold text-gray-800 mb-2">{{ __('Happy Anniversary! 💍') }}</h2>
        <p class="text-xl text-gray-600 mb-6">
            {{ __('Dear') }} <span class="font-semibold text-red-600">{{ auth()->user()->member->first_name }}</span>,
        </p>
        <p class="text-gray-600 mb-8 italic">
            "{{ __('Two are better than one... for if they fall, one will lift up his fellow.') }}"
            <br><span class="text-sm text-gray-500">- {{ __('Ecclesiastes 4:9-10') }}</span>
        </p>
        <p class="text-gray-700 mb-8">
            {{ __('Wishing you another year of love, joy, and blessings together!') }}
            <br>- {{ __('Your Church Family') }}
        </p>

        {{-- Anniversary Wishes --}}
        @php
            $anniversaryWishes = auth()->user()->notifications()
                ->where('type', 'App\Notifications\AnniversaryGreetingNotification')
                ->whereDate('created_at', today())
                ->get();
        @endphp

        @if($anniversaryWishes->count() > 0)
        <button onclick="toggleAnniversaryWishes()" id="viewAnniversaryWishesBtn" class="text-red-600 font-semibold underline mb-6 hover:text-red-800 transition block mx-auto">
            💌 {{ __('View') }} {{ $anniversaryWishes->count() }} {{ __('Wishes from Friends') }}
        </button>
        
        <div id="anniversaryWishesList" class="hidden bg-red-50 rounded-lg p-4 mb-6 text-left max-h-40 overflow-y-auto">
            <h3 class="text-sm font-bold text-red-600 mb-2">💌 {{ __('Wishes from Friends') }}:</h3>
            <ul class="space-y-2">
                @foreach($anniversaryWishes as $wish)
                <li class="text-sm text-gray-700 border-b border-red-100 pb-1 last:border-0">
                    <span class="font-semibold">{{ $wish->data['sender'] ?? 'A Friend' }}:</span>
                    "{{ __($wish->data['message'] ?? 'Happy Anniversary!') }}"
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        <button onclick="closeAnniversaryModal()" class="bg-gradient-to-r from-red-500 to-pink-600 text-white px-6 py-2 rounded-full font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 transition">
            {{ __('Thank You! ❤️') }}
        </button>
    </div>
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-open Birthday Modal
        const birthdayModal = document.getElementById('birthdayModal');
        if (birthdayModal) {
            const seenKey = 'birthday_seen_' + new Date().toISOString().split('T')[0];
            if (!localStorage.getItem(seenKey)) {
                openBirthdayModal(true); 
            }
        }

        // Auto-open Anniversary Modal
        const anniversaryModal = document.getElementById('anniversaryModal');
        if (anniversaryModal) {
            const seenKey = 'anniversary_seen_' + new Date().toISOString().split('T')[0];
            if (!localStorage.getItem(seenKey)) {
                openAnniversaryModal(true); 
            }
        }
    });

    // Birthday Functions
    function openBirthdayModal(isAuto = false) {
        const modal = document.getElementById('birthdayModal');
        const card = document.getElementById('birthdayCard');
        
        if (modal) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                card.classList.remove('scale-0');
                card.classList.add('scale-100');
            }, 100);

            if (isAuto) launchConfetti();
        } else {
            alert("{{ __('It is not your birthday today, so the celebration card is hidden!') }}");
        }
    }

    function closeBirthdayModal() {
        const modal = document.getElementById('birthdayModal');
        const card = document.getElementById('birthdayCard');
        
        const seenKey = 'birthday_seen_' + new Date().toISOString().split('T')[0];
        localStorage.setItem(seenKey, 'true');

        if (card) {
            card.classList.remove('scale-100');
            card.classList.add('scale-0');
        }
        setTimeout(() => { if(modal) modal.classList.add('hidden'); }, 300);
    }

    function toggleWishes() {
        const list = document.getElementById('wishesList');
        const btn = document.getElementById('viewWishesBtn');
        if (list && list.classList.contains('hidden')) {
            list.classList.remove('hidden');
            if(btn) btn.style.display = 'none';
        } else if (list) {
            list.classList.add('hidden');
        }
    }

    // Anniversary Functions
    function openAnniversaryModal(isAuto = false) {
        const modal = document.getElementById('anniversaryModal');
        const card = document.getElementById('anniversaryCard');
        
        if (modal) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                card.classList.remove('scale-0');
                card.classList.add('scale-100');
            }, 100);

            if (isAuto) launchConfetti();
        } else {
            alert("{{ __('It is not your anniversary today, so the celebration card is hidden!') }}");
        }
    }

    function closeAnniversaryModal() {
        const modal = document.getElementById('anniversaryModal');
        const card = document.getElementById('anniversaryCard');
        
        const seenKey = 'anniversary_seen_' + new Date().toISOString().split('T')[0];
        localStorage.setItem(seenKey, 'true');

        if (card) {
            card.classList.remove('scale-100');
            card.classList.add('scale-0');
        }
        setTimeout(() => { if(modal) modal.classList.add('hidden'); }, 300);
    }

    function toggleAnniversaryWishes() {
        const list = document.getElementById('anniversaryWishesList');
        const btn = document.getElementById('viewAnniversaryWishesBtn');
        if (list && list.classList.contains('hidden')) {
            list.classList.remove('hidden');
            if(btn) btn.style.display = 'none';
        } else if (list) {
            list.classList.add('hidden');
        }
    }

    function launchConfetti() {
        var duration = 5 * 1000;
        var animationEnd = Date.now() + duration;
        var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 60 };

        function randomInOut(min, max) {
            return Math.random() * (max - min) + min;
        }

        var interval = setInterval(function() {
            var timeLeft = animationEnd - Date.now();
            if (timeLeft <= 0) return clearInterval(interval);
            var particleCount = 50 * (timeLeft / duration);
            confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInOut(0.1, 0.3), y: Math.random() - 0.2 } }));
            confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInOut(0.7, 0.9), y: Math.random() - 0.2 } }));
        }, 250);
    }
</script>

@stack('scripts')
</body>
</html>
