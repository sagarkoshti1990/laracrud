<!-- number input -->
@php
    $optionPointer = 0;
    $optionValue = old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' ));
    if(isset($field['attributes']['value']) && $field['attributes']) {
        $optionValue = $field['attributes']['value'];
    }
@endphp
<div @include('crud.inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
        @include('crud.inc.field_translatable_icon')
    @endif

    @if(isset($field['attributes']['prefix']) || isset($field['attributes']['suffix'])) <div class="input-group"> @endif
        @if(isset($field['attributes']['prefix'])) <div class="input-group-addon">{!! $field['attributes']['prefix'] !!}</div> @endif
        <input
        	type="number"
        	name="{{ $field['name'] }}"
            id="{{ $field['name'] }}"
            value="{{ $optionValue }}"
            @include('crud.inc.field_attributes')
        	>
        @if(isset($field['attributes']['suffix'])) <div class="input-group-addon">{!! $field['attributes']['suffix'] !!}</div> @endif

    @if(isset($field['attributes']['prefix']) || isset($field['attributes']['suffix'])) </div> @endif

    @if ($errors->has($field['name']))
        <span class="help-block">{{ $errors->first($field['name']) }}</span>
    @endif
    
    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>
