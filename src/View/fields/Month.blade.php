@php
    $value = isset($field['default']) ? $field['default'] : '';
    if(old($field['name'])) {
        try {
            $value = \Carbon::parse(old($field['name']))->format('m-Y');
        } catch (\Exception $e) {
            $value = old($field['name']);
        }
    } else if (isset($field['value'])) {
        $value = \Carbon::parse($field['value'])->format('m-Y');
    }
@endphp
@component(config('stlc.view_path.inc.input_group','stlc::inc.input_group'),['field' => $field])
    @slot('onInput')
        <input
            type="text"
            name="{{ $field['name'] }}"
            value="{{ $value }}"
            @include(config('stlc.view_path.inc.field_attributes','stlc::inc.field_attributes'))
            data-format="MM-YYYY"
            data-template="MMM YYYY"
            data-width = "49%";
        >
    @endslot
@endcomponent
@pushonce('crud_fields_styles')
<style>
    .combodate{display: block;flex: 1 1 0%;}
    .combodate select{display: inline-block;}
</style>
@endpushonce
{{-- FIELD JS - will be loaded in the after_scripts section --}}
@pushonce('crud_fields_scripts')
    <script src="{{ asset('public/js/combodate.js') }}"></script>
    <script>
        jQuery(document).ready(function($){
            $('.month_combodate').combodate({
                customClass:'{{ (isset($errors) && $errors->has($field['name'])) ? "form-control is-invalid" : "form-control" }}',
                minYear: 1970,
                maxYear: 2100
            });
        });
    </script>
@endpushonce