@php
	$from_view = $from_view ?? 'index';
@endphp
@if ($crud->hasAccess('create') && isset($item->id))
    <a href="{{ url($crud->route.'/create?copy='.$item->getKey()) }}@if(isset($src))?src={{ $src }}@endif"
		@attributes($crud,$from_view.'.button.clone',[
			'class'=>'btn btn-flat btn-sm mb-2 bg-teal',
			'data-toggle'=>"tooltip", 'title'=>trans('stlc.clone')
		])
	>
	@if(!isset($text) || (isset($text) && $text != true))
		<i class="{{ config('stlc.view.icon.button.clone','fa fa-clone') }}"></i>
	@else
		{{ trans('stlc.clone') }}
	@endif
	</a>
@endif