<!-- number input -->
@php
    $optionPointer = 0;
    $optionValue = old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' ));
    if(isset($field['attributes']['value']) && $field['attributes']) {
        $optionValue = $field['attributes']['value'];
    }
@endphp
<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_translatable_icon')
    @endif
    @if(isset($field['attributes']['prefix']) || isset($field['attributes']['suffix'])) <div class="input-group"> @endif
    @if(isset($field['attributes']['prefix'])) <div class="input-group-addon">{!! $field['attributes']['prefix'] !!}</div> @endif
    <input type="number" name="{{ $field['name'] }}" value="{{ $optionValue }}" @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')>
    @if(isset($field['attributes']['suffix'])) <div class="input-group-addon">{!! $field['attributes']['suffix'] !!}</div> @endif
    @if(isset($field['attributes']['prefix']) || isset($field['attributes']['suffix'])) </div> @endif
    @if ($errors->has($field['name']))
        <span class="help-block">{{ $errors->first($field['name']) }}</span>
    @endif
    @if (isset($field['hint'])){{-- HINT --}}
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>
