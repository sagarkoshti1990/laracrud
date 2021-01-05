@php
	$from_view = $from_view ?? 'index';
@endphp
@if ($crud->hasAccess('restore') && isset($item->id))
	<a
		href="{{ url($crud->route.'/'.$item->getKey().'/restore') }}"
		src="@if(isset($src)){{ $src }}@else{{ $crud->route }}@endif"
		@attributes($crud,$from_view.'.button.restore',[
			'class'=>'btn btn-flat btn-sm mb-2 bg-purple',
			'method' => "post",'data-button-type'=>"confirm_ajax",
			'stlc-title'=>trans('stlc.restore_confirm'),
			'stlc-text'=>trans('stlc.restore_confirm_text'),
			'data-toggle'=>"tooltip", 'title'=>trans('stlc.restore')
		])
	>
		@if(!isset($text) || (isset($text) && $text != true))
		<i class="{{ config('stlc.view.icon.button.restore','fa fa-history') }}"></i>
		@else
            {{ trans('stlc.restore') }}
		@endif
	</a>
@endif