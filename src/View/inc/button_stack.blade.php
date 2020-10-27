@php
	$item = $item ?? $crud->row ?? [];
	if(isset($name) && is_array($name) && count($name) && !isset($item->deleted_at)) {
		$buttons = $crud->buttons->where('stack', $stack)->whereIn('name', $name);
	} else if(isset($item->deleted_at) && $crud->module->name == "SpecificationMasters" && count($item->childs)){
		$buttons = collect();
	} elseif (isset($item->deleted_at)) {
		$buttons = $crud->buttons->where('name', 'restore');
	} else {
		$buttons = $crud->buttons->where('stack', $stack)->whereNotIn('name', ['restore']);
	}
@endphp
@if ($buttons->count())
	@foreach ($buttons as $button)
	  @if ($button->type == 'model_function')
			{!! $item->{$button->content}(); !!}
		@elseif($button->type == 'view' && $button->name == "delete")
			@include($button->content, ['src' => $delete_src ?? null])
		@else
			@include($button->content)
	  @endif
	@endforeach
@endif