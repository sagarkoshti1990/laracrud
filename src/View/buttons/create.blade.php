@if ($crud->hasAccess('create'))
	@if(isset($crud->create_button) && $crud->create_button == 'inline')
		<a href="{{ url($crud->route.'/create') }}" class="btn btn-warning btn-flat pull-right">
			<i class="glyphicon glyphicon-plus mr5"></i> {{ $crud->label }}
		</a>
	@else
		<div class="wrap-new-lead">
			<div class="new-lead">
				{{-- <a class="btn btn-default" data-toggle="modal" data-target="{{ $crud->modal ?? '#add_modal' }}" title="{{ $crud->label }}" href="#"><i class="fa fa-plus"></i></a> --}}
				<a href="{{ url($crud->route.'/create') }}" class="btn btn-default">
					<i class="fa fa-plus"></i>
				</a>
			</div>
		</div>
	@endif
@endif