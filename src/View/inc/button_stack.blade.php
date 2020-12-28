@php
	$item = $item ?? $crud->row ?? [];
	if(isset($name) && is_array($name) && count($name) && !isset($item->deleted_at)) {
		$buttons = $crud->buttons->where('stack', $stack)->whereIn('name', $name);
	} elseif (isset($item->deleted_at)) {
		$buttons = $crud->buttons->where('type', 'deleted');
	} else {
		$buttons = $crud->buttons->where('stack', $stack)->where('type','!=','deleted');
	}
@endphp
@if ($buttons->count())
	@foreach ($buttons as $button)
		@if ($button->type == 'model_function')
			{!! $item->{$button->content}(); !!}
		@elseif($button->name == "delete" || $button->type == 'deleted')
			@include($button->content, ['src' => $delete_src ?? null])
		@else
			@include($button->content)
		@endif
	@endforeach
@endif