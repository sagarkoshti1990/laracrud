<!-- bootstrap datetimepicker input -->
@php
if (isset($field['value']) && ( $field['value'] instanceof \Carbon\Carbon || $field['value'] instanceof \Jenssegers\Date\Date )) {
    $field['value'] = $field['value']->format('Y-m-d H:i:s');
}
    $field_language = isset($field['datetime_picker_options']['language'])?$field['datetime_picker_options']['language']:\App::getLocale();
@endphp
<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    <input type="hidden" name="{{ $field['name'] }}" value="{{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' )) }}">
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_translatable_icon')
    @endif
    <div class="input-group date">
        <input
            data-bs-datetimepicker="{{ isset($field['datetime_picker_options']) ? json_encode($field['datetime_picker_options']) : '{}'}}"
            type="text"
            @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
        >
        <div class="input-group-append">
            <div class="input-group-text"><span class="fa fa-calendar"></span></div>
        </div>
    </div>
    @if ($errors->has($field['name']))
        <span class="help-block">{{ $errors->first($field['name']) }}</span>
    @endif
    @if (isset($field['hint'])){{-- HINT --}}
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>
@pushonce('crud_fields_styles')
<!-- datetime picker -->
<link rel="stylesheet" href="{{ asset('node_modules/bootstrap-datetimepicker-npm/build/css/bootstrap-datetimepicker.min.css') }}">
<style>
    .bootstrap-datetimepicker-widget.dropdown-menu {
        display: inline-block;
        z-index: 99999 !important;
        width: 100% !important;
        padding-left: 20px !important;
    }
</style>
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
                    time: "fa fa-clock-o",
                    date: "fa fa-calendar",
                    up: "fa fa-chevron-up",
                    down: "fa fa-chevron-down",
                    previous: 'fa fa-chevron-left',
                    next: 'fa fa-chevron-right',
                    today: 'fa fa-sun-o',
                    clear: 'fa fa-trash',
                    close: 'fa fa-times'
                },
                format: 'DD/MM/YYYY hh:mm:a',
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