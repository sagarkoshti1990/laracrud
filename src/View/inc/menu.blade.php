@php
    $prefix = $prefix ?? config('stlc.route_prefix', 'admin');
@endphp
<!-- Header Navbar: style can be found in header.less -->
<nav class="main-header navbar navbar-expand {{config('stlc.navbar','navbar-white navbar-light')}}" role="navigation">
    <!-- Sidebar toggle button-->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    <ul class="navbar-nav flex-row ml-md-auto d-none d-md-flex">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge">15</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="left: inherit; right: 0px;">
                <span class="dropdown-item dropdown-header">15 Notifications</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-envelope mr-2"></i> 4 new messages
                    <span class="float-right text-muted text-sm">3 mins</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-users mr-2"></i> 8 friend requests
                    <span class="float-right text-muted text-sm">12 hours</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-file mr-2"></i> 3 new reports
                    <span class="float-right text-muted text-sm">2 days</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
            </div>
        </li>
        <li class="nav-item dropdown user-menu" data-toggle="tooltip" title="Profile">
            <a href="#" class="nav-item nav-link dropdown-toggle mr-md-2" data-toggle="dropdown" id="bd-versions-user">
                <img src="{{ \Module::user()->profile_pic() }}" class="user-image" alt="">
                <span class="hidden-xs">{{ \Module::user()->name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-right">
                <!-- User image -->
                <li class="user-header">
                    <img src="{{ \Module::user()->profile_pic() }}" class="rounded-circle" alt="">
                    <p></p>
                    @forelse(\Module::user()->roles as $role)
                        <span class="badge bg-green text-center">{{ $role->label }}</span>
                    @empty
                        <span class="badge bg-red bg-red text-center">Not Assigned</span>
                    @endforelse
                </li>
                <!-- Menu Body -->
                @if(\Module::user()->isSuperAdmin())
                    <li class="user-body p-0">
                        <ul class="nav nav-pills nav-justified">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="{{ url(config('stlc.stlc_route_prefix').'/modules') }}">
                                    <i class="fa fa-briefcase"></i> Modules
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="{{ url(config('stlc.route_prefix').'/settings') }}">
                                    <i class="fa fa-cog"></i> Setting
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                <!-- Menu Footer-->
                <li class="user-footer text-center">
                    {{-- <div class="float-left">
                        <a href="#" class="btn btn-default btn-flat">Profile</a>
                    </div>
                    <div class="float-right">
                        <a href="#" class="btn btn-default btn-flat">Sign out</a>
                    </div> --}}
                    <a href="{{ route('stlc.account.info') }}" class="btn bg-green btn-flat btn-sm mb5">
                        <span><i class="fa fa-user-circle"></i> My Account</span>
                    </a>
                    <a href="{{ url($prefix.'/logout') }}" class="btn bg-orange btn-flat btn-sm mb5">
                        <span><i class="fa fa-sign-out"></i>Logout</span>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>