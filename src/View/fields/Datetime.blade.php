<!-- html5 datetime input -->
<?php
if (isset($field['value']) && ( $field['value'] instanceof \Carbon\Carbon || $field['value'] instanceof \Jenssegers\Date\Date )) {
    $field['value'] = $field['value']->toDateTimeString();
}
?>
@component(config('stlc.view_path.inc.input_group','stlc::inc.input_group'),['field' => $field])
    @slot('onInput')
    <input
        type="datetime-local"
        name="{{ $field['name'] }}"
        value="{{ old($field['name']) ? strftime('%Y-%m-%dT%H:%M:%S', strtotime(old($field['name']))) : (isset($field['value']) ? strftime('%Y-%m-%dT%H:%M:%S', strtotime($field['value'])) : ((isset($field['default']) && $field['default'] != "") ? strftime('%Y-%m-%dT%H:%M:%S', strtotime($field['default'])) : '' )) }}"
        @include(config('stlc.view_path.inc.field_attributes','stlc::inc.field_attributes'))
    >
    @endslot
@endcomponent
