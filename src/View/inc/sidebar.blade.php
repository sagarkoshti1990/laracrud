@if (Auth::check())
<aside class="main-sidebar pt-5">
	<!-- sidebar: style can be found in sidebar.less -->
	<section class="sidebar">
		<!-- Sidebar user panel -->
		<div class="user-panel">
			<div class="float-left image">
				<img src="{{ Auth::user()->profile_pic() }}" class="rounded-circle" alt="">
			</div>
			<div class="float-left info">
				<p>{{ Auth::user()->name }}</p>
				<a href="{{ url(config('stlc.route_prefix').'/logout') }}">
					<i class="fa fa-sign-out"></i>
					<span>Logout</span>
				</a>
			</div>
		</div>
		<!-- sidebar menu: : style can be found in sidebar.less -->
		<ul class="sidebar-menu" data-widget="tree">
			<li class="header">Administration</li>
			@php $menuItems = \Sagartakle\Laracrud\Models\Menu::where("parent", null)->orderBy('rank', 'asc')->get(); @endphp
			@foreach ($menuItems as $menu)
				@if($menu->type == "module")
					@access($menu->name)
						@if(isset($crud->module->id) && ($crud->module->name == $menu->name))
							@php echo \CustomHelper::print_menu($menu); @endphp
						@else
							@php echo \CustomHelper::print_menu($menu); @endphp
						@endif
					@endaccess
				@elseif($menu->type == "page")
					@pageAccess($menu->name)
						@if(isset($crud->module->id) && ($crud->module->name == $menu->name))
							@php echo \CustomHelper::print_menu($menu); @endphp
						@else
							@php echo \CustomHelper::print_menu($menu); @endphp
						@endif
					@endpageAccess
				@else
					@php echo \CustomHelper::print_menu($menu); @endphp
				@endif
			@endforeach
			{{-- <li><a href="{{url('how-to-use')}}"><i class="fa fa-book text-purple"></i> <span>How To Use</span></a></li> --}}
		</ul>
	</section>
	<!-- /.sidebar -->
</aside>
@endif