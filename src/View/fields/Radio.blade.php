<!-- radio -->
@php
    $optionPointer = 0;
    $optionValue = old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' ));
@endphp
<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <div>
            <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
        </div>
    @endif
    @if( isset($field['options']) && $field['options'] = (array)$field['options'] )
        @foreach ($field['options'] as $value => $label )
            @php ($optionPointer++) @endphp
            @if( isset($field['inline']) && $field['inline'])
                @if(isset($field['wrapperAttributes']['radio_inline']))
                    @php $field['attributes']['class'] = "form-check-input"; @endphp
                    <div class="form-check-inline icheck-primary">
                        <input
                            type="radio"
                            id="{{$field['name']}}_{{$optionPointer}}"
                            name="{{$field['name']}}"
                            value="{{$value}}"
                            {{$optionValue == $value ? ' checked': ''}}
                            @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
                        >
                        <label for="{{$field['name']}}_{{$optionPointer}}" class="form-check-label">
                            {!! $label !!}
                        </label>
                    </div>
                @else
                    <div class="radio-inline icheck-primary">
                        <input
                            type="radio"
                            id="{{$field['name']}}_{{$optionPointer}}"
                            name="{{$field['name']}}"
                            value="{{$value}}"
                            {{$optionValue == $value ? ' checked': ''}}
                            @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
                        >
                        <label for="{{$field['name']}}_{{$optionPointer}}">
                            {!! $label !!}
                        </label>
                    </div>
                @endif
            @else
            <div class="radio icheck-primary">
                <input
                    type="radio"
                    id="{{$field['name']}}_{{$optionPointer}}"
                    name="{{$field['name']}}"
                    value="{{$value}}"
                    {{$optionValue == $value ? ' checked ': ''}}
                    @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
                >
                <label for="{{$field['name']}}_{{$optionPointer}}"> 
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