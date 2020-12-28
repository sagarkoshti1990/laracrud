<!-- bootstrap timepicker input -->
@php
    if (isset($field['attributes']['value'])) {
        $field['value'] = \CrudHelper::date_format($field['attributes']['value'], 'data_save');
    }
    if (isset($field['value']) && ( $field['value'] instanceof \Carbon\Carbon || $field['value'] instanceof \Jenssegers\Date\Date )) {
        $field['value'] = $field['value']->format('Y-m-d');
    }
    $field_language = isset($field['date_picker_options']['language'])?$field['date_picker_options']['language']:\App::getLocale();
    $field['prefix'] = $field['prefix'] ?? '<span class="fa fa-clock"></span>';
@endphp
@component(config('stlc.view_path.inc.input_group','stlc::inc.input_group'),['field' => $field])
    @slot('onInput')
        <input
            data-bs-timepicker="{{ isset($field['date_picker_options']) ? json_encode($field['date_picker_options']) : '{}'}}"
            type="text"
            data-name="{{ $field['name'] }}"
            @include(config('stlc.view_path.inc.field_attributes','stlc::inc.field_attributes'))
            >
        <input
            type="hidden"
            name="{{ $field['name'] }}"
            value="{{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' )) }}"
            @include(config('stlc.view_path.inc.field_attributes','stlc::inc.field_attributes'))
        >
    @endslot
@endcomponent
@pushonce('crud_fields_styles')
    <!-- date picker -->
    <link rel="stylesheet" href="{{ asset('node_modules/bootstrap-datetimepicker-npm/build/css/bootstrap-datetimepicker.min.css') }}">
    <style>
        .bootstrap-datetimepicker-widget.dropdown-menu {
            display: inline-block;
            z-index: 99999 !important;
        }
    </style>
@endpushonce
{{-- FIELD JS - will be loaded in the after_scripts section --}}
@pushonce('crud_fields_scripts')
<script src="{{ asset('node_modules/bootstrap-datetimepicker-npm/build/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script>
        jQuery(document).ready(function($){
            $('[data-bs-timepicker]').each(function(){
                var $fake = $(this),
                $field = $fake.parents('.form-group').find('input[type="hidden"]'),
                $customConfig = $.extend({
                    icons: {
                        time: "fa fa-clock",
                        date: "fa fa-calendar",
                        up: "fa fa-chevron-up",
                        down: "fa fa-chevron-down",
                        previous: 'fa fa-chevron-left',
                        next: 'fa fa-chevron-right',
                        today: 'fa fa-sun',
                        clear: 'fa fa-trash',
                        close: 'fa fa-times'
                    },
                    format: 'LT',
                    // inline: true,
                    // sideBySide: true,
                    defaultDate: (typeof $field.val() != 'undefined' && $field.val() != "") ? moment($field.val(),'HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') : ''
                }, $fake.data('bs-datetimepicker'));
                $customConfig.locale = $customConfig['language'];
                delete($customConfig['language']);
                $picker = $fake.datetimepicker($customConfig);
                if(isset($picker.startDate)) {
                    $field.val($picker.startDate.format('HH:mm:ss'));
                }
                // $fake.on('keydown', function(e){
                //     e.preventDefault();
                //     return false;
                // });
                $picker.on('dp.change', function(e){
                    var sqlDate = e.date ? e.date.format('HH:mm:ss') : null;
                    $field.val(sqlDate).trigger('change');
                });
            });
        });
    </script>
@endpushonce