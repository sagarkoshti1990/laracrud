<!-- checkbox field -->
@php
    $optionValue = [];
    $optionPointer = 0;
    $field['wrapperAttributes']['class'] = "form-group";
    if((old($field['name'])) && is_array(old($field['name']))) {
        $optionValue = old($field['name']);
    } else if(old($field['name']) && is_array(json_decode(old($field['name'])))) {
        $optionValue = json_decode(old($field['name']), true);
    } else if((isset($field['value'])) && is_array(json_decode($field['value']))) {
        $optionValue = json_decode($field['value'], true);
    } else if((isset($field['default'])) && is_array(json_decode($field['default']))) {
        $optionValue = json_decode($field['default'], true);
    }
    $field['attributes']['class'] = $errors->has($field['name']) ? $field['attributes']['class'].' is-invalid' : $field['attributes']['class'];
@endphp
<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    <div>
        @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
            <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
        @endif
        <input type="hidden" name="{{ $field['name'] }}" value="">
    </div>
    @if( isset($field['options']) && $field['options'] = (array)$field['options'] )
        @foreach ($field['options'] as $value => $label )
            @php ($optionPointer++) @endphp
            @if( (isset($field['inline']) && $field['inline']) && (array_key_exists("inline", $field['attributes']) && $field['attributes']['inline']) )
            <div class="form-check checkbox-inline icheck-primary">
                <input
                    type="checkbox"
                    name="{{ $field['name'] }}[]"
                    id="{{$field['name']}}_{{$optionPointer}}"
                    value="{{$value}}"
                    {{ in_array($value, $optionValue) ? ' checked': ''}}
                    @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
                >
                <label for="{{$field['name']}}_{{$optionPointer}}" class="form-check-label"> {!! $label !!} </label>
            </div>
            @else
            <div class="form-check icheck-primary @if($errors->has($field['name'])) is-invalid @endif">
                <input
                    type="checkbox"
                    name="{{ $field['name'] }}[]"
                    id="{{$field['name']}}_{{$optionPointer}}"
                    value="{{$value}}"
                    {{ in_array($value, $optionValue) ? ' checked': ''}}
                    @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
                >
                <label for="{{$field['name']}}_{{$optionPointer}}" class="form-check-label">
                    {!! $label !!}
                </label>
            </div>
            @endif
        @endforeach
    @endif
    @if ($errors->has($field['name']))
        <div class="is-invalid"></div><span class="invalid-feedback">{{ $errors->first($field['name']) }}</span>
    @endif
    @if (isset($field['hint'])){{-- HINT --}}
        <p class="form-text">{!! $field['hint'] !!}</p>
    @endif
</div>