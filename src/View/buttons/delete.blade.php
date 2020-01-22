@if ($crud->hasAccess('delete'))
	<a
		href="{{ url($crud->route.'/'.$entry->getKey()) }}"
		src="@if(isset($src)){{ $src }}@else{{ $crud->route }}@endif"
		@if(!isset($text) || (isset($text) && $text != true))
			class="btn @if(isset($class_btn)){{ $class_btn }}@else btn-flat btn-sm @endif bg-maroon-gradient"
		@endif
		data-button-type="delete"
	>
		@if(!isset($text) || (isset($text) && $text != true))
			<i class="fa fa-trash"></i>
		@else
			Delete
		@endif
	</a>
@endif