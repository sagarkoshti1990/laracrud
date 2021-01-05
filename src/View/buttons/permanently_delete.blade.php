@php
	$from_view = $from_view ?? 'index';
@endphp
@if ($crud->hasAccess('permanently_delete') && isset($item->id))
	<a
		href="{{ url($crud->route.'/'.$item->getKey().'/permanently_delete') }}"
		src="@if(isset($src)){{ $src }}@else{{ $crud->route }}@endif"
		@attributes($crud,$from_view.'.button.permanently_delete',[
			'class'=>'btn btn-flat btn-sm mb-2 bg-danger',
			'method' => "post",'data-button-type'=>"confirm_ajax",
			'stlc-title'=>trans('stlc.permanently_delete_confirm'),
			'stlc-text'=>trans('stlc.permanently_delete_confirm_text'),
			'data-toggle'=>"tooltip", 'title'=>trans('stlc.permanently_delete')
		])
	>
		@if(!isset($text) || (isset($text) && $text != true))
		<i class="{{ config('stlc.view.icon.button.permanently_delete','fa fa-trash-alt') }}"></i>
		@else
            {{ trans('stlc.permanently_delete') }}
		@endif
	</a>
@endif