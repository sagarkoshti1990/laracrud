<!-- radio -->
@php
    $optionPointer = 0;
    $optionValue = old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' ));
@endphp
<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <div>
            <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
            @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_translatable_icon')
        </div>
    @endif
    @if( isset($field['options']) && $field['options'] = (array)$field['options'] )
        @foreach ($field['options'] as $value => $label )
            @php ($optionPointer++) @endphp
            @if( isset($field['inline']) && $field['inline'] )
                @if(isset($field['wrapperAttributes']['radio_inline']))
                    @php $field['attributes']['class'] = "form-check-input"; @endphp
                    <div class="form-check-inline">
                        <label for="{{$field['name']}}_{{$optionPointer}}" class="form-check-label">
                            <input
                                type="radio"
                                {{-- id="{{$field['name']}}_{{$optionPointer}}" --}}
                                name="{{$field['name']}}"
                                value="{{$value}}"
                                {{$optionValue == $value ? ' checked': ''}}
                                @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
                            >
                            {!! $label !!}
                        </label>
                    </div>
                @else
                    <div class="radio-inline">
                        <label for="{{$field['name']}}_{{$optionPointer}}">
                            <input
                                type="radio"
                                {{-- id="{{$field['name']}}_{{$optionPointer}}" --}}
                                name="{{$field['name']}}"
                                value="{{$value}}"
                                {{$optionValue == $value ? ' checked': ''}}
                                @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
                            >
                            {!! $label !!}
                        </label>
                    </div>
                @endif
            @else
            <div class="radio">
                <label for="{{$field['name']}}_{{$optionPointer}}"> 
                    <input
                        type="radio"
                        {{-- id="{{$field['name']}}_{{$optionPointer}}" --}}
                        name="{{$field['name']}}"
                        value="{{$value}}"
                        {{$optionValue == $value ? ' checked ': ''}}
                        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
                    >
                    {!! $label !!}
                </label>
            </div>
            @endif
        @endforeach
    @endif
    @if ($errors->has($field['name']))
        <span class="help-block">{{ $errors->first($field['name']) }}</span>
    @endif
    @if (isset($field['hint'])){{-- HINT --}}
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>
@pushonce('after_styles')
    <!-- switchery CSS -->
@endpushonce
{{-- FIELD JS - will be loaded in the after_scripts section --}}
@pushonce('crud_fields_scripts')
    <!-- Switchery JavaScript -->
<script>
    $(function () {
        //Flat red color scheme for iCheck
        $('input[name="{{ $field['name'] }}"]').iCheck({
            checkboxClass: "{{ isset($field['attributes']['check_color']) ? $field['attributes']['check_color'] : 'icheckbox_square-purple' }}",
            radioClass: "{{ isset($field['attributes']['check_color']) ? $field['attributes']['check_color'] : 'icheckbox_square-purple' }}",
            increaseArea: '20%'
        });
    });
</script>
@endpushonce