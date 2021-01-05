@php
    $field['prefix'] = $field['prefix'] ?? '<span class="fa fa-eye-slash f-show-password"></span>';
@endphp
@component(config('stlc.view_path.inc.input_group','stlc::inc.input_group'),['field' => $field])
    @slot('onInput')
        <input
            type="password"
            name="{{ $field['name'] }}"
            @include(config('stlc.view_path.inc.field_attributes','stlc::inc.field_attributes'))
        >
    @endslot
@endcomponent