@if ($crud->hasAccess('deleted'))
	<a
		href="{{ url($crud->route.'/'.$entry->getKey().'/restore') }}"
		src="@if(isset($src)){{ $src }}@else{{ $crud->route }}@endif"
		@if(!isset($text) || (isset($text) && $text != true))
			class="btn @if(isset($class_btn)){{ $class_btn }}@else btn-flat btn-sm mb-2 @endif bg-purple"
		@endif
		title="{{ trans('stlc.restore') }}" data-toggle="tooltip"
		data-button-type="confirm_ajax"
		method="post",
		stlc-title="{{ trans('stlc.restore_confirm') }}"
		stlc-text="{{ trans('stlc.restore_confirm_text') }}"
	>
		@if(!isset($text) || (isset($text) && $text != true))
			<i class="fa fa-history"></i>
		@else
            {{ trans('stlc.restore') }}
		@endif
	</a>
@endif