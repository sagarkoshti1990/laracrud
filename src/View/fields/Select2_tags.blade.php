@php 
    if(isset($field['multiple']) && $field['multiple']) {
        $value = (old($field['name']) && is_array(old($field['name']))) ? old($field['name']) : (old($field['name']) && is_array(json_decode(old($field['name'])))) ? json_decode(old($field['name'])) : (isset($field['value']) ? json_decode($field['value']) : (isset($field['default']) ? $field['default'] : '' ));
    } else {
        $value = old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' ));
    }
    if(isset($value) && !empty($value)) {
        if(is_array($value)) {
            foreach($value as $arrValue) {
                if(!in_array($arrValue,$field['options']) && !empty($arrValue)) {
                    $field['options'][] = $arrValue;
                }
            }
        } else {
            if(!in_array($value,$field['options'])) {
                $field['options'][] = $value;
            }
        }
    }
@endphp
<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
    @endif
    <select
        @if(isset($field['multiple']) && $field['multiple']) name="{{ $field['name'] }}[]" @else name="{{ $field['name'] }}" @endif
        style="width: 100%"
        @if(isset($field['multiple']) && $field['multiple']) multiple @endif
        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes', ['default_class' =>  'form-control select2_field_tag'])
        >
        @if(!(isset($field['multiple']) && $field['multiple']))
            @if(!(isset($field['attributes']['allows_null'])) || (isset($field['attributes']['allows_null']) && ($field['attributes']['allows_null'])))
                <option value="">{{ 'Select '.str_replace('*','',strip_tags($field['label'])) }}</option>
            @endif
        @endif
        @if (count($field['options']))
            @foreach ($field['options'] as $key => $optionvalue)
                <option value="{{ $optionvalue }}"
                @if(isset($field['multiple']) && $field['multiple'])
                    @if (isset($value) && is_array($value) && in_array($optionvalue, $value))
                        selected
                    @endif
                @else
                    @if (isset($value) && $value == $optionvalue)
                        selected
                    @endif
                @endif
                >{{ $optionvalue }}</option>
            @endforeach
        @endif
    </select>
    @if ($errors->has($field['name']))
        <div class="is-invalid"></div><span class="invalid-feedback">{{ $errors->first($field['name']) }}</span>
    @endif
    @if (isset($field['hint'])){{-- HINT --}}
        <p class="form-text">{!! $field['hint'] !!}</p>
    @endif
</div>
@pushonce('crud_fields_styles')
    <link href="{{ asset('node_modules/admin-lte/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('node_modules/admin-lte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endpushonce
@pushonce('crud_fields_scripts')
    <script src="{{ asset('node_modules/admin-lte/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        function readonly_select(objs, action) {
            if (action===true) {
                objs.prepend('<div class="disabled-select"></div>');
            } else {
                $(".disabled-select", objs).remove();
            }
        }
        jQuery(document).ready(function($) {
            // trigger select2 for each untriggered select2 box
            $('.select2_field_tag').each(function (i, obj) {
                if (!$(obj).hasClass("select2-hidden-accessible")) {
                    $(obj).select2({
                        theme:"bootstrap4",
                        tags: true
                    });
                    if(isset($(this).attr('readonly')) && $(this).attr('readonly') == "readonly"){
                        readonly_select($(this).parents('.form-group').find('span.select2'), true);
                    }
                }
            });
            readonly_select($(this).parents('.form-group').find('span.select2'), true);
        });
    </script>
@endpushonce