@php
	$from_view = $from_view ?? 'index';
@endphp
@if ($crud->hasAccess('delete'))
	<a
		href="{{ url($crud->route.'/'.$item->getKey()) }}"
		src="@if(isset($src)){{ $src }}@else{{ $crud->route }}@endif"
		@attributes($crud,$from_view.'.button.delete',[
			'class'=>'btn btn-flat btn-sm mb-2 bg-maroon',
			'method' => "DELETE",'data-button-type'=>"confirm_ajax",
			'stlc-title'=>trans('stlc.delete_confirm'),
			'stlc-text'=>trans('stlc.delete_confirm_text'),
			'data-toggle'=>"tooltip", 'title'=>trans('stlc.delete')
		])
	>
		@if(!isset($text) || (isset($text) && $text != true))
		<i class="{{ config('stlc.view.icon.button.delete','fa fa-trash') }}"></i>
		@else
			{{ trans('stlc.delete') }}
		@endif
	</a>
@endif