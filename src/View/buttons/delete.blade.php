@if ($crud->hasAccess('delete'))
	<a
		href="{{ url($crud->route.'/'.$item->getKey()) }}"
		src="@if(isset($src)){{ $src }}@else{{ $crud->route }}@endif"
		@if(!isset($text) || (isset($text) && $text != true))
			class="btn @if(isset($class_btn)){{ $class_btn }}@else btn-flat btn-sm mb-2 @endif bg-maroon-gradient"
		@endif
		title="{{ trans('stlc.delete') }}"
		data-button-type="confirm_ajax"
		method="DELETE",
		stlc-title="{{ trans('stlc.delete_confirm') }}"
		stlc-text="{{ trans('stlc.delete_confirm_text') }}"
	>
		@if(!isset($text) || (isset($text) && $text != true))
			<i class="fa fa-trash"></i>
		@else
			{{ trans('stlc.delete') }}
		@endif
	</a>
@endif