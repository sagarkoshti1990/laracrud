@php
	$from_view = $from_view ?? 'index';
@endphp
@if ($crud->hasAccess('view') && isset($item->id))
	<a href="{{ url($crud->route.'/'.$item->getKey()) }}@if(isset($src))?src={{ $src }}@endif"
		@attributes($crud,$from_view.'.button.preview',[
			'class'=>'btn btn-flat btn-sm mb-2 bg-purple',
			'data-toggle'=>"tooltip", 'title'=>trans('stlc.preview')
		])
	>
	@if(!isset($text) || (isset($text) && $text != true))
		<i class="{{ config('stlc.view.icon.button.preview','fa fa-eye') }}"></i>
	@else
		View
	@endif
	</a>
@endif