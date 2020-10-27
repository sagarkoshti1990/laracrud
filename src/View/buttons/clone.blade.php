@if ($crud->hasAccess('create') && isset($item->id))
    <a href="{{ url($crud->route.'/create?copy='.$item->getKey()) }}@if(isset($src))?src={{ $src }}@endif"
		@if(!isset($text) || (isset($text) && $text != true))
			class="btn @if(isset($class_btn)){{ $class_btn }}@else btn-flat btn-sm mb-2 @endif bg-teal"
		@endif
		data-toggle="tooltip"
		title="Copy"
	>
	@if(!isset($text) || (isset($text) && $text != true))
		<i class="fa fa-clone"></i>
	@else
        Clone
	@endif
	</a>
@endif