@php
    $prefix = $prefix ?? config('lara.base.route_prefix', 'admin');
@endphp
<ul class="navbar-nav flex-row ml-md-auto d-none d-md-flex">
    @if (Auth::guest())
        <li class="nav-item">
            <a class="nav-item nav-link" href="{{ url($prefix.'/login') }}">{{ trans('base.login') }}</a>
        </li>
        @if (config('lara.base.registration_open'))
            <li class="nav-item">
                <a class="nav-item nav-link" href="{{ url($prefix.'/register') }}">{{ trans('base.register') }}</a>
            </li>
        @endif
    @else
    <li class="nav-item dropdown notifications-menu">
        <a class="nav-item nav-link dropdown-toggle mr-md-2" href="#" id="bd-versions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-bell-o"></i>
            <span class="badge badge-warning">9</span>
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="bd-versions">
            <ul class="menu">
                <li>
                    <a href="#"><i class="fa fa-users text-aqua"></i> 5 new members joined today</a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-warning text-yellow"></i> Very long description here that may not fit into the
                        page and may cause design problems
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-users text-red"></i> 5 new members joined
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-shopping-cart text-green"></i> 25 sales made
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-user text-red"></i> You changed your username
                    </a>
                </li>
            </ul>
        </div>
    </li>
    <li class="nav-item dropdown user-menu" data-toggle="tooltip" title="Profile">
        <a href="#" class="nav-item nav-link dropdown-toggle mr-md-2" data-toggle="dropdown" id="bd-versions-user">
            <img src="{{ Auth::user()->profile_pic() }}" class="user-image" alt="">
            <span class="hidden-xs">{{ Auth::user()->name }}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="bd-versions-user">
            <!-- User image -->
            <div class="user-header"  style="height:auto">
                <img src="{{ Auth::user()->profile_pic() }}" class="img-circle" alt="">
                <p></p>
                @forelse(\Auth::user()->roles as $role)
                    <span class="badge bg-green text-center">{{ $role->label }}</span>
                @empty
                    <span class="badge bg-red bg-red text-center">Not Assigned</span>
                @endforelse
                {{--  @endforeach  --}}
            </div>
            <!-- Menu Body -->
            @if(\Auth::user()->isSuperAdmin())
                <div class="user-body p-0">
                    <ul class="nav nav-pills nav-justified">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="{{ url(config('lara.base.route_prefix').'/modules') }}">
                                <i class="fa fa-briefcase"></i> Modules
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="{{ url(config('lara.base.route_prefix').'/settings') }}">
                                <i class="fa fa-cog"></i> 
                                {{ trans('base.setting') }}
                            </a>
                        </li>
                    </ul>
                </div> 
            @endif
            <!-- Menu Footer-->
            <div class="user-footer">
                <div class="text-center"><!-- pull-left -->
                    {{-- <a href="{{ route('lara.account.info') }}" class="btn bg-green btn-flat btn-sm mb5">
                        <span><i class="fa fa-user-circle-o"></i> {{ trans('base.my_account') }}</span>
                    </a> --}}
                    <a href="{{ url($prefix.'/logout') }}" class="btn bg-orange btn-flat btn-sm mb5">
                        <i class="fa fa-sign-out"></i>
                        {{ trans('base.logout') }}
                    </a>
                </div>
            </div>
        </div>
    </li>
    @endif
</ul>