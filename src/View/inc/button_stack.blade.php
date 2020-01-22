@php
		if(isset($name) && is_array($name) && count($name) && !isset($entry->deleted_at)) {
			$buttons = $crud->buttons->where('stack', $stack)->whereIn('name', $name);
		} else if(isset($entry->deleted_at) && $crud->module->name == "SpecificationMasters" && count($entry->childs)){
			$buttons = collect();
		} elseif (isset($entry->deleted_at)) {
			$buttons = $crud->buttons->where('name', 'restore');
		} else {
			$buttons = $crud->buttons->where('stack', $stack)->whereNotIn('name', ['restore']);
		}
@endphp
@if ($buttons->count())
	@foreach ($buttons as $button)
	  @if ($button->type == 'model_function')
			{!! $entry->{$button->content}(); !!}
		@elseif($button->type == 'view' && $button->name == "delete" && isset($delete_src))
			@include($button->content, ['src' => $delete_src])
		@else
			@include($button->content)
	  @endif
	@endforeach
@endif