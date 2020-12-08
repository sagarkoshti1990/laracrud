<!-- bootstrap datepicker input -->
@php
    if (isset($field['attributes']['value'])) {
        $field['value'] = \CrudHelper::date_format($field['attributes']['value'], 'data_save');
    }
    if (isset($field['value']) && ( $field['value'] instanceof \Carbon\Carbon || $field['value'] instanceof \Jenssegers\Date\Date )) {
        $field['value'] = $field['value']->format('Y-m-d');
    }
    $field_language = isset($field['date_picker_options']['language'])?$field['date_picker_options']['language']:\App::getLocale();
@endphp
<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
    @endif
    <div class="input-group date">
        @if(isset($field['prefix'])) <div class="input-group-prepend"><span class="input-group-text">{!! $field['prefix'] !!}</span></div> @endif
        <input
            data-bs-datepicker="{{ isset($field['date_picker_options']) ? json_encode($field['date_picker_options']) : '{}'}}"
            type="text"
            data-name="{{ $field['name'] }}"
            @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
            >
        <div class="input-group-append"><span class="input-group-text">@if(isset($field['suffix'])) {!! $field['suffix'] !!} @else <span class="fa fa-calendar"></span> @endif </span></div>
    </div>
    <input
        type="hidden"
        name="{{ $field['name'] }}"
        value="{{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' )) }}"
        @foreach ($field['attributes'] as $attribute => $value)
            @if (is_string($attribute) && $attribute == "required")
                {{ $attribute }}="{{ $value }}"
            @endif
        @endforeach
    >
    @if ($errors->has($field['name']))
        <div class="is-invalid"></div><span class="invalid-feedback">{{ $errors->first($field['name']) }}</span>
    @endif
    @if (isset($field['hint'])){{-- HINT --}}
        <p class="form-text">{!! $field['hint'] !!}</p>
    @endif
</div>
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
            $('[data-bs-datepicker]').each(function(){
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
                    format: 'DD/MM/YYYY',
                    // inline: true,
                    // sideBySide: true,
                    defaultDate: $field.val()
                }, $fake.data('bs-datetimepicker'));
                $customConfig.locale = $customConfig['language'];
                delete($customConfig['language']);
                $picker = $fake.datetimepicker($customConfig);
                if(isset($picker.startDate)) {
                    $field.val($picker.startDate.format('YYYY-MM-DD'));
                }
                // $fake.on('keydown', function(e){
                //     e.preventDefault();
                //     return false;
                // });
                $picker.on('dp.change', function(e){
                    var sqlDate = e.date ? e.date.format('YYYY-MM-DD') : null;
                    $field.val(sqlDate).trigger('change');
                });
            });
        });
    </script>
@endpushonce