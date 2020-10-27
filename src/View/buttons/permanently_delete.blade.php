@if ($crud->hasAccess('permanently-delete') && isset($item->id))
	<a
		href="{{ url($crud->route.'/'.$item->getKey().'/permanently_delete') }}"
		src="@if(isset($src)){{ $src }}@else{{ $crud->route }}@endif"
		@if(!isset($text) || (isset($text) && $text != true))
			class="btn @if(isset($class_btn)){{ $class_btn }}@else btn-flat btn-sm mb-2 @endif btn-danger"
		@endif
		title="{{ trans('stlc.permanently_delete') }}" data-toggle="tooltip"
		data-button-type="confirm_ajax"
		method="post",
		stlc-title="{{ trans('stlc.permanently_delete_confirm') }}"
		stlc-text="{{ trans('stlc.permanently_delete_confirm_text') }}"
	>
		@if(!isset($text) || (isset($text) && $text != true))
			<i class="fa fa-trash-alt"></i>
		@else
            {{ trans('stlc.permanently_delete') }}
		@endif
	</a>
@endif