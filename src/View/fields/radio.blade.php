<!-- radio -->
@php
    $optionPointer = 0;
    $optionValue = old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' ));
@endphp
<div @include('crud.inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <div>
            <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
            @include('crud.inc.field_translatable_icon')
        </div>
    @endif
    @if( isset($field['options']) && $field['options'] = (array)$field['options'] )
        @foreach ($field['options'] as $value => $label )
            @php ($optionPointer++) @endphp
            @if( isset($field['inline']) && $field['inline'] )
            <div class="radio-inline">
                <input
                    type="radio"
                    id="{{$field['name']}}_{{$optionPointer}}"
                    name="{{$field['name']}}"
                    value="{{$value}}"
                    {{$optionValue == $value ? ' checked': ''}}
                    @include('crud.inc.field_attributes')
                >
                <label for="{{$field['name']}}_{{$optionPointer}}"> {!! $label !!} </label>
            </div>
            @else
            <div class="radio">
                <input
                    type="radio"
                    id="{{$field['name']}}_{{$optionPointer}}"
                    name="{{$field['name']}}"
                    value="{{$value}}"
                    {{$optionValue == $value ? ' checked ': ''}}
                    @include('crud.inc.field_attributes')
                >
                <label for="{{$field['name']}}_{{$optionPointer}}"> {!! $label !!} </label>
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

{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
{{-- If a field type is shown radio btn on a form, the CSS and JS will only be loaded once --}}
 @if ($crud->checkIfOnce($field))
 
    @push('after_styles')
		<!-- switchery CSS -->
    @endpush
    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('after_scripts')
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
    @endpush
@endif