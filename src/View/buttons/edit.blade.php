@php
	$from_view = $from_view ?? 'index';
@endphp
@if ($crud->hasAccess('edit'))
	<a href="{{ url($crud->route.'/'.$item->getKey().'/edit') }}@if(isset($src))?src={{ $src }}@endif"
		@attributes($crud,$from_view.'.button.edit',[
			'class'=>'btn btn-flat btn-sm mb-2 bg-orange',
			'data-toggle'=>"tooltip", 'title'=>trans('stlc.edit')
		])
		>
		@if(!isset($text) || (isset($text) && $text != true))
		<i class="{{ config('stlc.view.icon.button.edit','fa fa-edit') }}"></i>
		@else
			{{trans('stlc.edit')}}
		@endif
	</a>
@endif