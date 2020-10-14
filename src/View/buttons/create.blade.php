@if ($crud->hasAccess('create'))
	@if(isset($crud->create_button) && $crud->create_button == 'inline')
		<a href="{{ url($crud->route.'/create') }}" class="btn btn-warning btn-flat float-right">
			<i class="glyphicon glyphicon-plus mr5"></i> {{ $crud->label }}
		</a>
	@else
		{{-- <a class="btn btn-primary btn-lg position-fixed" style="right:2rem;bottom:2rem;border-radius:100%" data-toggle="modal" data-target="{{ $crud->modal ?? '#add_modal' }}" title="{{ $crud->label }}" href="#"><i class="fa fa-plus"></i></a> --}}
		<a href="{{ url($crud->route.'/create') }}" class="btn btn-primary btn-lg position-fixed" style="right:2rem;bottom:2rem;border-radius:100%;z-index: 9999;" title="{{ $crud->label }}"><i class="fa fa-plus"></i></a>
	@endif
@endif