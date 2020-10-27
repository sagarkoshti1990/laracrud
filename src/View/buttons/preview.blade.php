@if ($crud->hasAccess('view') && isset($item->id))
	<a href="{{ url($crud->route.'/'.$item->getKey()) }}@if(isset($src))?src={{ $src }}@endif"
		@if(!isset($text) || (isset($text) && $text != true))
			class="btn @if(isset($class_btn)){{ $class_btn }}@else btn-flat btn-sm mb-2 @endif bg-purple"
		@endif
		data-toggle="tooltip" title="View Info"
	>
	@if(!isset($text) || (isset($text) && $text != true))
		<i class="fa fa-eye"></i>
	@else
		View
	@endif
	</a>
@endif