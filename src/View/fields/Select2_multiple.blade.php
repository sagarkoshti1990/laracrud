@php 
    $value = old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' ));
@endphp
@component(config('stlc.view_path.inc.input_group','stlc::inc.input_group'),['field' => $field])
    @slot('onInput')
        <select
            name="{{ $field['name'] }}[]"
            style="width: 100%"
            @include(config('stlc.view_path.inc.field_attributes','stlc::inc.field_attributes'))
            multiple>
            @if (isset($field['model']))
                @if(isset($field['model']) && is_object($field['model']))
                    @php $selec_list = $field['model']->get(); @endphp
                @else
                    @php $selec_list = $field['model']::get(); @endphp
                @endif
                @foreach ($selec_list as $connected_entity_item)
                    @php
                        $option_text = "";
                        if(is_array($field['attribute'])) {
                            $option_text = \CustomHelper::get_represent_attr($connected_entity_item,$field['attribute']);
                        } else {
                            $option_text = \CustomHelper::get_represent_attr($connected_entity_item,null,$field['attribute']);
                        }
                    @endphp
                    <option value="{{ $connected_entity_item->getKey() }}"
                        @if( (isset($value)) && (is_array($value) && in_array($connected_entity_item->getKey(), $value)) || (is_string($value) && in_array($connected_entity_item->getKey(), collect(json_decode($value))->toArray())) ) )
                            selected
                        @endif
                    >{{ $option_text }}</option>
                @endforeach
            @elseif (isset($field['options']) && is_array($field['options']) && count($field['options']))
                @foreach ($field['options'] as $key => $optionValue)
                    <option value="{{ $key }}"
                    @if( (isset($value)) && (is_array($value) && in_array($key, $value)) || (is_string($value) && in_array($key, collect(json_decode($value))->toArray())) ) )
                            selected
                        @endif
                    >{{ $optionValue }}</option>
                @endforeach
            @endif
        </select>
    @endslot
@endcomponent
@pushonce('crud_fields_styles')
    <link href="{{ asset('node_modules/admin-lte/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('node_modules/admin-lte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endpushonce
@pushonce('crud_fields_scripts')
    <!-- include select2 js-->
    <script src="{{ asset('node_modules/admin-lte/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        jQuery(document).ready(function($) {
            // trigger select2 for each untriggered select2_multiple box
            $('.select2_multiple').each(function (i, obj) {
                if (!$(obj).hasClass("select2-hidden-accessible"))
                {
                    $(obj).select2({
                        theme:"bootstrap4",
                        placeholder: $(this).attr('placeholder')
                    });
                }
            });
        });
    </script>
@endpushonce