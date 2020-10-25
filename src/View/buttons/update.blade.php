@if ($crud->hasAccess('edit'))
	<a href="{{ url($crud->route.'/'.$item->getKey().'/edit') }}@if(isset($src))?src={{ $src }}@endif"
		@if(!isset($text) || (isset($text) && $text != true))
			class="btn @if(isset($class_btn)){{ $class_btn }}@else btn-flat btn-sm mb-2 @endif bg-orange"
		@endif
		title="Edit"
		>
		@if(!isset($text) || (isset($text) && $text != true))
			<i class="fa fa-edit"></i>
		@else
			Edit
		@endif
	</a>
@endif