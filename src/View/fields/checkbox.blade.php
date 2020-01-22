<!-- checkbox field -->
@php
    $optionValue = [];
    $field['wrapperAttributes']['class'] = "form-group clearfix";
    if((isset($field['name'])) && is_array(json_decode($field['name']))) {
        $optionValue = json_decode($field['name'], true);
    } else if((isset($field['value'])) && is_array(json_decode($field['value']))) {
        $optionValue = json_decode($field['value'], true);
    } else if((isset($field['default'])) && is_array(json_decode($field['default']))) {
        $optionValue = json_decode($field['default'], true);
    }
@endphp
<div @include('crud.inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    <div>
        @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
            <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
        @endif
        <input type="hidden" name="{{ $field['name'] }}" value="">
        @include('crud.inc.field_translatable_icon')
    </div>
    @if( isset($field['options']) && $field['options'] = (array)$field['options'] )
        @foreach ($field['options'] as $value => $label )
            @if( (isset($field['inline']) && $field['inline']) && (array_key_exists("inline", $field['attributes']) && $field['attributes']['inline']) )
            <div class="checkbox checkbox-inline">
                <input
                    type="checkbox"
                    name="{{ $field['name'] }}[]"
                    value="{{$value}}"
                    {{ in_array($value, $optionValue) ? ' checked': ''}}
                    @include('crud.inc.field_attributes')
                >
                <label for="{{$field['name']}}"> {!! $label !!} </label>
            </div>
            @else
            <div class="checkbox col-md-4">
                <input
                    type="checkbox"
                    name="{{ $field['name'] }}[]"
                    value="{{$value}}"
                    {{ in_array($value, $optionValue) ? ' checked': ''}}
                    @include('crud.inc.field_attributes')
                >
                <label for="{{$field['name']}}"> {!! $label !!} </label>
            </div>
            @endif
        @endforeach
    @endif
    
    @if ($errors->has($field['name']))
        <span class="help-block">{{ $errors->first($field['name']) }}</span>
    @endif

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>
