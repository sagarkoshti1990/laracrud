@component(config('stlc.view_path.inc.input_group','stlc::inc.input_group'),['field' => $field])
    @slot('onInput')
        <select
            name="{{ $field['name'] }}"
            @include(config('stlc.view_path.inc.field_attributes','stlc::inc.field_attributes'))
            >
            @if(!(isset($field['attributes']['allows_null'])) || (isset($field['attributes']['allows_null']) && ($field['attributes']['allows_null'])))
                <option value="">{{ (isset($field['attributes']['select_option']) && $field['attributes']['select_option'] == false) ? '' : "Select" }} {{ str_replace('*','',strip_tags($field['label'])) }}</option>
            @endif

            @if (isset($field['model']))
                @foreach ($field['model']::all() as $connected_entity_item)
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