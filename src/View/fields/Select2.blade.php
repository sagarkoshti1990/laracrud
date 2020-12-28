@component(config('stlc.view_path.inc.input_group','stlc::inc.input_group'),['field' => $field])
    @slot('onInput')
        <select
            name="{{ $field['name'] }}"
            style="width: 100%"
            @include(config('stlc.view_path.inc.field_attributes','stlc::inc.field_attributes'), ['default_class' =>  'form-control select2_field select2-purple'])
            >
            @if(!(isset($field['attributes']['allows_null'])) || (isset($field['attributes']['allows_null']) && ($field['attributes']['allows_null'])))
                <option value="">{{ (isset($field['attributes']['select_option']) && $field['attributes']['select_option'] == false) ? '' : "Select" }} {{ str_replace('*','',strip_tags($field['label'])) }}</option>
            @endif
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
                        @if ( ( old($field['name']) && old($field['name']) == $connected_entity_item->getKey() ) || (isset($field['value']) && $connected_entity_item->getKey()==$field['value']))
                            selected
                        @endif
                    >{{ $option_text }}</option>
                @endforeach
            @elseif (isset($field['options']) && is_array($field['options']) && count($field['options']))
                @foreach ($field['options'] as $key => $value)
                    <option value="{{ $key }}"
                        @if (isset($field['value']) && ($key==$field['value'] || (is_array($field['value']) && in_array($key, $field['value'])))
                            || ( ! is_null( old($field['name']) ) && old($field['name']) == $key))
                            selected
                        @endif
                    >{{ $value }}</option>
                @endforeach
            @endif
        </select>
    @endslot
@endcomponent
{{-- FIELD JS - will be loaded in the after_scripts section --}}
@pushonce('crud_fields_styles')
    <link href="{{ asset('node_modules/admin-lte/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('node_modules/admin-lte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endpushonce
@pushonce('crud_fields_scripts')
    <!-- include select2 js-->
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
            $('.select2_field').each(function (i, obj) {
                if (!$(obj).hasClass("select2-hidden-accessible"))
                {
                    $(obj).select2({
                        theme:"bootstrap4"
                    });
                    
                    if(isset($(this).attr('readonly')) && $(this).attr('readonly') == "readonly"){
                        // console.log($(this).attr('readonly'));
                        readonly_select($(this).parents('.form-group').find('span.select2'), true);
                    }
                }
            });
            readonly_select($(this).parents('.form-group').find('span.select2'), true);
        });
    </script>
@endpushonce