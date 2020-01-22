@if ($crud->reorder)
	@if ($crud->hasAccess('reorder'))
	  <a href="{{ url($crud->route.'/reorder') }}" class="btn @if(isset($class_btn)){{ $class_btn }}@else btn-xs mt5 @endif bg-default" data-style="zoom-in"><span class="ladda-label"><i class="fa fa-arrows"></i> {{ trans('crud.reorder') }} {{ $crud->labelPlural }}</span></a>
	@endif
@endif