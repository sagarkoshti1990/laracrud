@php
    $prefix = $prefix ?? config('stlc.route_prefix', 'admin');
@endphp
<ul class="navbar-nav flex-row ml-md-auto d-none d-md-flex">
    @if (Auth::guest())
        <li class="nav-item">
            <a class="nav-item nav-link" href="{{ url($prefix.'/login') }}">{{ trans('base.login') }}</a>
        </li>
        @if (config('stlc.registration_open'))
            <li class="nav-item">
                <a class="nav-item nav-link" href="{{ url($prefix.'/register') }}">{{ trans('base.register') }}</a>
            </li>
        @endif
    @else
    <li class="nav-item dropdown notifications-menu">
        <a class="nav-item nav-link dropdown-toggle mr-md-2" href="#" id="bd-versions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            <i class="fa fa-bell-o"></i>
            <span class="badge badge-warning">9</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="header">You have 10 notifications</li>
            <li>
                <ul class="menu">
                    <li><a href="#"><i class="fa fa-users text-aqua"></i> 5 new members joined today</a></li>
                    <li>
                        <a href="#">
                            <i class="fa fa-warning text-yellow"></i>
                            Very long description here that may not fit into the
                            page and may cause design problems
                        </a>
                    </li>
                    <li><a href="#"><i class="fa fa-users text-red"></i> 5 new members joined</a></li>
                    <li><a href="#"><i class="fa fa-shopping-cart text-green"></i> 25 sales made</a></li>
                    <li><a href="#"><i class="fa fa-user text-red"></i> You changed your username</a></li>
                </ul>
            </li>
            <li class="footer"><a href="#">View all</a></li>
        </ul>
    </li>
    <li class="nav-item dropdown user-menu" data-toggle="tooltip" title="Profile">
        <a href="#" class="nav-item nav-link dropdown-toggle mr-md-2" data-toggle="dropdown" id="bd-versions-user">
            <img src="{{ Auth::user()->profile_pic() }}" class="user-image" alt="">
            <span class="hidden-xs">{{ Auth::user()->name }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-right">
            <!-- User image -->
            <li class="user-header">
                <img src="{{ Auth::user()->profile_pic() }}" class="rounded-circle" alt="">
                <p></p>
                @forelse(\Auth::user()->roles as $role)
                    <span class="badge bg-green text-center">{{ $role->label }}</span>
                @empty
                    <span class="badge bg-red bg-red text-center">Not Assigned</span>
                @endforelse
            </li>
            <!-- Menu Body -->
            @if(\Auth::user()->isSuperAdmin())
                <li class="user-body p-0">
                    <ul class="nav nav-pills nav-justified">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="{{ url(config('stlc.route_prefix').'/modules') }}">
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
            <li class="user-footer">
                {{-- <div class="pull-left">
                    <a href="#" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                    <a href="#" class="btn btn-default btn-flat">Sign out</a>
                </div> --}}
                <a href="{{ route('stlc.account.info') }}" class="btn bg-green btn-flat btn-sm mb5">
                    <span><i class="fa fa-user-circle-o"></i> My Account</span>
                </a>
                <a href="{{ url($prefix.'/logout') }}" class="btn bg-orange btn-flat btn-sm mb5">
                    <span><i class="fa fa-sign-out"></i>logout</span>
                </a>
            </li>
        </ul>
    </li>
    @endif
</ul>