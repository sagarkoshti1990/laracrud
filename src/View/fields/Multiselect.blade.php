<!-- select multiple -->
@php
    $field['attributes']['class'] = (isset($errors) && $errors->has($field['name'])) ? $field['attributes']['class'].' is-invalid' : $field['attributes']['class'];
    $value = old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' ));
@endphp
@component(config('stlc.view_path.inc.input_group','stlc::inc.input_group'),['field' => $field])
    @slot('onInput')
        <select
            name="{{ $field['name'] }}[]"
            @include(config('stlc.view_path.inc.field_attributes','stlc::inc.field_attributes'))
            multiple>

            @if (!isset($field['allows_null']) || $field['allows_null'])
                <option value="">None</option>
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
                        @if (isset($value) && (is_array($value) && in_array($connected_entity_item->getKey(), $value)))
                            selected
                        @endif
                    >{{ $option_text }}</option>
                @endforeach
            @elseif (isset($field['options']) && is_array($field['options']) && count($field['options']))
                @foreach ($field['options'] as $key => $optionValue)
                    <option value="{{ $optionValue }}"
                        @if (isset($value) && ((is_array($value) && in_array($optionValue, $value)) || (is_array(json_decode($value)) && in_array($optionValue, json_decode($value)))))
                            selected
                        @endif
                    >{{ $optionValue }}</option>
                @endforeach
            @endif
        </select>
    @endslot
@endcomponent