<!-- number input -->
@php
    $optionPointer = 0;
    $optionValue = old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' ));
    if(isset($field['attributes']['value']) && $field['attributes']) {
        $optionValue = $field['attributes']['value'];
    }
@endphp
@component(config('stlc.view_path.inc.input_group','stlc::inc.input_group'),['field' => $field])
    @slot('onInput')
        <input
            type="number" name="{{ $field['name'] }}" value="{{ $optionValue }}"
            @include(config('stlc.view_path.inc.field_attributes','stlc::inc.field_attributes'))
        >
    @endslot
@endcomponent
