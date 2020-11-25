<!-- select multiple -->
@php
    $field['attributes']['class'] = $errors->has($field['name']) ? $field['attributes']['class'].' is-invalid' : $field['attributes']['class'];
    $value = old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' ));
@endphp
<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
    @endif
    <select
        name="{{ $field['name'] }}[]"
        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
        multiple>

		@if (!isset($field['allows_null']) || $field['allows_null'])
            <option value="">None</option>
		@endif

        @if (isset($field['model']))
            @foreach ($field['model']::all() as $connected_entity_item)
                <option value="{{ $connected_entity_item->getKey() }}"
                    @if (isset($value) && (is_array($value) && in_array($connected_entity_item->getKey(), $value)))
                        selected
                    @endif
                >{{ $connected_entity_item->{$field['attribute']} }}</option>
            @endforeach
        @elseif (isset($field['options']) && is_array($field['options']) && count($field['options']))
            @foreach ($field['options'] as $key => $optionValue)
                <option value="{{ $optionValue }}"
                    @if (isset($value) && ((is_array($value) && in_array($optionValue, $value)) || (is_array(json_decode($value)) && in_array($optionValue, json_decode($value)))))
                        selected
                    @endif
                >{{ $optionValue }}</option>
            @endforeach
        @endif
	</select>
    @if ($errors->has($field['name']))
        <div class="is-invalid"></div><span class="invalid-feedback">{{ $errors->first($field['name']) }}</span>
    @endif
    @if (isset($field['hint'])){{-- HINT --}}
        <p class="form-text">{!! $field['hint'] !!}</p>
    @endif
</div>