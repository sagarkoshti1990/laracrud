@component(config('stlc.view_path.inc.input_group','stlc::inc.input_group'),['field' => $field])
    @slot('onInput')
        <input
            type="text"
            name="{{ $field['name'] }}"
            value="{{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' )) }}"
            @include(config('stlc.view_path.inc.field_attributes','stlc::inc.field_attributes'))
        >
    @endslot
@endcomponent

