@php
    if(isset($attributes)) {
        if(is_string($attributes)) {
            $attributes = ['string' => $attributes];
        } else if(is_object($attributes)) {
            $attributes = collect($attributes)->toArray();
        }
    } else {
        $attributes = [];
    }
@endphp
@foreach ($attributes as $attribute => $value)
	@if (is_string($attribute)) {{ $attribute }}="{{ $value }}"@endif
@endforeach