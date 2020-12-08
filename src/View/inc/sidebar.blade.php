@if (isset(\Module::user()->id))
<aside class="main-sidebar elevation-4 {{config('stlc.sidebar','sidebar-dark-primary')}}">
	<!-- sidebar: style can be found in sidebar.less -->
	<a href="{{ url('/') }}" class="brand-link {{config('stlc.brand_logo','')}}">
		{{-- <img src="{{ asset('public/img/icon.png') }}" class="brand-image img-circle elevation-2" style="opacity: .8"> --}}
		<span class="brand-text font-weight-light">{!! config('stlc.logo_lg','STLC') !!}</span>
	</a>
	
    <div class="sidebar">
		<!-- Sidebar user panel -->
		<div class="user-panel mt-3 mb-3 d-flex">
			<div class="image">
				<img src="{{ \Module::user()->profile_pic() }}" class="img-circle elevation-2" alt="User Image">
			</div>
			<div class="info pt-0 pb-2">
				<span class="nav-header">{{ \Module::user()->name }}</span>
				<a class="d-block small" href="{{ url(config('stlc.route_prefix').'/logout') }}">
					<i class="fa fa-sign-out"></i>
					<span>Logout</span>
				</a>
			</div>
		</div>
		<!-- sidebar menu: : style can be found in sidebar.less -->
		<nav class="mt-2">
			<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
				@php $menuItems = config('stlc.menu_model')::where("parent", null)->orderBy('rank', 'asc')->get(); @endphp
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
			</ul>
		</nav>
    </div>
	<!-- /.sidebar -->
</aside>
@endif