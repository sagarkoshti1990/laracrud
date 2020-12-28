<!-- bootstrap datetimepicker input -->
@php
    if (isset($field['value']) && ( $field['value'] instanceof \Carbon\Carbon || $field['value'] instanceof \Jenssegers\Date\Date )) {
        $field['value'] = $field['value']->format('Y-m-d H:i:s');
    }
    $field_language = isset($field['datetime_picker_options']['language'])?$field['datetime_picker_options']['language']:\App::getLocale();
    $field['prefix'] = $field['prefix'] ?? '<span class="fa fa-calendar-alt"></span>';
@endphp
@component(config('stlc.view_path.inc.input_group','stlc::inc.input_group'),['field' => $field])
    @slot('onInput')
        <input
            data-bs-datetimepicker="{{ isset($field['datetime_picker_options']) ? json_encode($field['datetime_picker_options']) : '{}'}}"
            type="text"
            @include(config('stlc.view_path.inc.field_attributes','stlc::inc.field_attributes'))
        >
        <input
            @include(config('stlc.view_path.inc.field_attributes','stlc::inc.field_attributes'))
            type="hidden"
            name="{{ $field['name'] }}" value="{{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' )) }}"
        >
    @endslot
@endcomponent
@pushonce('crud_fields_styles')
<link rel="stylesheet" href="{{ asset('node_modules/bootstrap-datetimepicker-npm/build/css/bootstrap-datetimepicker.min.css') }}">
@endpushonce
{{-- FIELD JS - will be loaded in the after_scripts section --}}
@pushonce('crud_fields_scripts')
<script src="{{ asset('node_modules/bootstrap-datetimepicker-npm/build/js/bootstrap-datetimepicker.min.js') }}"></script>
<script>
    jQuery(document).ready(function($){
        $('[data-bs-datetimepicker]').each(function(){
            var $fake = $(this),
            $field = $fake.parents('.form-group').find('input[type="hidden"]'),
            $customConfig = $.extend({
                icons: {
                    time: "fa fa-clock",date: "fa fa-calendar",up: "fa fa-chevron-up",
                    down: "fa fa-chevron-down",previous: 'fa fa-chevron-left',next: 'fa fa-chevron-right',
                    today: 'fa fa-sun',clear: 'fa fa-trash',close: 'fa fa-times'
                },
                format: '{{ config("stlc.date_format.datetime_picker","DD/MM/YYYY hh:mm:a") }}',
                sideBySide: true,
                //  minDate:new Date(),
                useCurrent: false,
                defaultDate: $field.val()
            }, $fake.data('bs-datetimepicker'));
            $customConfig.locale = $customConfig['language'];
            delete($customConfig['language']);
            $picker = $fake.datetimepicker($customConfig);
            $fake.on('keydown', function(e){
                e.preventDefault();
                return false;
            });
            $picker.on('dp.change', function(e){
                var sqlDate = e.date ? e.date.format('YYYY-MM-DD HH:mm:ss') : null;
                $field.val(sqlDate);
            });
        });
    });
</script>
@endpushonce