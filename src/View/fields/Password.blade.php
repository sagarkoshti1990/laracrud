@php
    $field['attributes']['class'] = $field['attributes']['class']." f-show-password";
@endphp
<!-- password -->
<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_translatable_icon')
    @endif
    <div class="has-feedback">
        <div class="input-group">
            @if(isset($field['prefix'])) <div class="input-group-prepend"><span class="input-group-text">{!! $field['prefix'] !!}<span></div> @endif
            <input
                type="password"
                name="{{ $field['name'] }}"
                @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
            >
            <div class="input-group-append"><span class="input-group-text">
                @if(isset($field['suffix']))
                    {!! $field['suffix'] !!}
                @else
                <i class="fa fa-eye-slash"></i>
                @endif
            <span></div>
        </div>
    </div>
    @if ($errors->has($field['name']))
        <span class="help-block">{{ $errors->first($field['name']) }}</span>
    @endif
    @if (isset($field['hint'])){{-- HINT --}}
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>