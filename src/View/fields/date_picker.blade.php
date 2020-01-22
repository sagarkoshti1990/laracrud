<!-- bootstrap datepicker input -->

@php
        // if the column has been cast to Carbon or Date (using attribute casting)
    // get the value as a date string
    if (isset($field['attributes']['value'])) {
        $field['value'] = \CrudHelper::date_format($field['attributes']['value'], 'data_save');
    }
    if (isset($field['value']) && ( $field['value'] instanceof \Carbon\Carbon || $field['value'] instanceof \Jenssegers\Date\Date )) {
        $field['value'] = $field['value']->format('Y-m-d');
    }

    $field_language = isset($field['date_picker_options']['language'])?$field['date_picker_options']['language']:\App::getLocale();
@endphp

<div @include('crud.inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
        @include('crud.inc.field_translatable_icon')
    @endif
    <div class="input-group date">
        <input
            data-bs-datepicker="{{ isset($field['date_picker_options']) ? json_encode($field['date_picker_options']) : '{}'}}"
            type="text"
            data-name="{{ $field['name'] }}"
            @foreach ($field['attributes'] as $attribute => $value)
                @if (is_string($attribute) && $attribute != "required")
                    {{ $attribute }}="{{ $value }}"
                @endif
            @endforeach
            >
        <div class="input-group-addon">
            <span class="glyphicon glyphicon-calendar"></span>
        </div>
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
        <span class="help-block">{{ $errors->first($field['name']) }}</span>
    @endif
    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>

@if ($crud->checkIfOnce($field))
    {{-- FIELD CSS - will be loaded in the after_styles section --}}
    @push('crud_fields_styles')
        <link rel="stylesheet" href="{{ asset('node_modules/bootstrap-datetimepicker-npm/build/css/bootstrap-datetimepicker.min.css') }}">
        <style>
            .bootstrap-datetimepicker-widget.dropdown-menu {
                display: inline-block;
                z-index: 99999 !important;
            }
        </style>
    @endpush

    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
        <script src="{{ asset('node_modules/bootstrap-datetimepicker-npm/build/js/bootstrap-datetimepicker.min.js') }}"></script>
        <script>
            jQuery(document).ready(function($){
                $.validator.setDefaults({ 
                    ignore: ['.ignore'],
                    // any other default options and/or rules
                });
                $('[data-bs-datepicker]').each(function(){
                    var $fake = $(this),
                    $field = $fake.parents('.form-group').find('input[type="hidden"]'),
                    $customConfig = $.extend({
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
    @endpush
@endif
{{-- End of Extra CSS and JS --}}
