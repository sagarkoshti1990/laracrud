<!-- html5 datetime input -->
<?php
if (isset($field['value']) && ( $field['value'] instanceof \Carbon\Carbon || $field['value'] instanceof \Jenssegers\Date\Date )) {
    $field['value'] = $field['value']->toDateTimeString();
}
?>
<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_translatable_icon')
    @endif
    <input
        type="datetime-local"
        name="{{ $field['name'] }}"
        value="{{ old($field['name']) ? strftime('%Y-%m-%dT%H:%M:%S', strtotime(old($field['name']))) : (isset($field['value']) ? strftime('%Y-%m-%dT%H:%M:%S', strtotime($field['value'])) : ((isset($field['default']) && $field['default'] != "") ? strftime('%Y-%m-%dT%H:%M:%S', strtotime($field['default'])) : '' )) }}"
        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
    >
    @if ($errors->has($field['name']))
        <span class="help-block">{{ $errors->first($field['name']) }}</span>
    @endif
    @if (isset($field['hint'])){{-- HINT --}}
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>
