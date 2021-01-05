@php
	$from_view = $from_view ?? 'index';
@endphp
@if ($crud->hasAccess('create') || $crud->hasAccess('restore') || $crud->hasAccess('permanently-delete'))
	<div class="dropdown">
		<button @attributes($crud,$from_view.'.button.create',[
			'class'=>'btn btn-primary btn-lg position-fixed',
			'type'=>"button", 'id'=>"dropdownMenuButton",
			'style'=>"right:2rem;bottom:2rem;border-radius:100%;z-index: 9999;font-size:1.25rem !important;",
			'data-toggle'=>"dropdown", 'aria-haspopup'=>"true", 'aria-expanded'=>"false"
		])><i class="{{ config('stlc.view.icon.button.create','fa fa-plus') }}"></i></button>
		<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
			@if($crud->hasAccess('restore') || $crud->hasAccess('permanently-delete'))
				@if(isset($_GET['__deleted__']) && $_GET['__deleted__'] == "true")
					<a class="dropdown-item" href="{{ url($crud->route) }}">
						{{ trans('stlc.list') }} {{ $crud->labelPlural }}
					</a>
				@else
					<a class="dropdown-item" href="{{ url($crud->route) }}?__deleted__=true">
						{{ trans('stlc.deleted') }} {{ $crud->labelPlural }}
					</a>
				@endif
			@endif
			@if($crud->hasAccess('create'))
				<a class="dropdown-item" href="#" data-toggle="modal" data-target="#add_modal">{{ trans('stlc.quick_add') }} {{ $crud->label }}</a>
				<a class="dropdown-item" href="{{ url($crud->route.'/create') }}" title="{{ $crud->label }}">{{ trans('stlc.add') }} {{ $crud->label }}</a>
			@endif
		</div>
	</div>
@endif