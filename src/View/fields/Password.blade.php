<!-- password -->
<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_translatable_icon')
    @endif
    <div class="has-feedback">
        <input
            type="password"
            name="{{ $field['name'] }}"
            @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes',['class' => 'form-control f-show-password'])
        >
        <i class="fa fa-eye-slash form-control-feedback"></i>
    </div>
    @if ($errors->has($field['name']))
        <span class="help-block">{{ $errors->first($field['name']) }}</span>
    @endif
    @if (isset($field['hint'])){{-- HINT --}}
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>