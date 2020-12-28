<!-- radio -->
@php
    $optionPointer = 0;
    $optionValue = old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' ));
@endphp
@component(config('stlc.view_path.inc.input_group','stlc::inc.input_group'),['field' => $field])
    @slot('onInput')
        <div>
        @if (isset($field['model']))
            @foreach ($field['model']::all() as $connected_entity_item)
                @php
                    $label = "";
                    $value = $connected_entity_item->id ?? null;
                    if(is_array($field['attribute'])) {
                        $label = \CustomHelper::get_represent_attr($connected_entity_item,$field['attribute']);
                    } else {
                        $label = \CustomHelper::get_represent_attr($connected_entity_item,null,$field['attribute']);
                    }
                @endphp
                @if( isset($field['inline']) && $field['inline'])
                    @php $field['attributes']['class'] = "form-check-input"; @endphp
                    <div class="form-check-inline icheck-primary">
                        <input
                            type="radio"
                            id="{{$field['name']}}_{{$optionPointer}}"
                            name="{{$field['name']}}"
                            value="{{$value}}"
                            {{$optionValue == $value ? ' checked': ''}}
                            @include(config('stlc.view_path.inc.field_attributes','stlc::inc.field_attributes'))
                        >
                        <label for="{{$field['name']}}_{{$optionPointer}}" class="form-check-label">{!! $label !!}</label>
                    </div>
                @else
                <div class="radio icheck-primary">
                    <input
                        type="radio"
                        id="{{$field['name']}}_{{$optionPointer}}"
                        name="{{$field['name']}}"
                        value="{{$value}}"
                        {{$optionValue == $value ? ' checked ': ''}}
                        @include(config('stlc.view_path.inc.field_attributes','stlc::inc.field_attributes'))
                    >
                    <label for="{{$field['name']}}_{{$optionPointer}}">{!! $label !!}</label>
                </div>
                @endif
            @endforeach
        @elseif( isset($field['options']) && $field['options'] = (array)$field['options'] )
            @foreach ($field['options'] as $value => $label)
                @php ($optionPointer++) @endphp
                @if( isset($field['inline']) && $field['inline'])
                    @php $field['attributes']['class'] = "form-check-input"; @endphp
                    <div class="form-check-inline icheck-primary">
                        <input
                            type="radio"
                            id="{{$field['name']}}_{{$optionPointer}}"
                            name="{{$field['name']}}"
                            value="{{$value}}"
                            {{$optionValue == $value ? ' checked': ''}}
                            @include(config('stlc.view_path.inc.field_attributes','stlc::inc.field_attributes'))
                        >
                        <label for="{{$field['name']}}_{{$optionPointer}}" class="form-check-label">{!! $label !!}</label>
                    </div>
                @else
                <div class="radio icheck-primary">
                    <input
                        type="radio"
                        id="{{$field['name']}}_{{$optionPointer}}"
                        name="{{$field['name']}}"
                        value="{{$value}}"
                        {{$optionValue == $value ? ' checked ': ''}}
                        @include(config('stlc.view_path.inc.field_attributes','stlc::inc.field_attributes'))
                    >
                    <label for="{{$field['name']}}_{{$optionPointer}}">{!! $label !!}</label>
                </div>
                @endif
            @endforeach
        @endif
        </div>
    @endslot
@endcomponent