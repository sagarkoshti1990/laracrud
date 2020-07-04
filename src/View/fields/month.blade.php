@php
    if (isset($field['value'])) {
        $field['value'] = \Carbon::parse($field['value'])->format('m-Y');
    }
@endphp
<!-- html5 month input -->
<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_translatable_icon')
    @endif
    <input
        type="text"
        name="{{ $field['name'] }}"
        value="{{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' )) }}"
        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
        data-format="MM-YYYY"
        data-template="MMM YYYY"
        data-width = "49%";
    >
    @if ($errors->has($field['name']))
        <span class="help-block">{{ $errors->first($field['name']) }}</span>
    @endif
    @if (isset($field['hint'])){{-- HINT --}}
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>
@pushonce('crud_fields_styles')
<style>
    .combodate{
        display: block;
    }
    .combodate select{
        display: inline-block;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        background-color: #fff;
        border: 1px solid rgba(33, 33, 33, 0.12);
        border-color: #d2d6de;
        border-radius: 0;
        box-shadow: none;
        color: #212121;
        height: 34px;
        background-image: none;
    }
</style>
@endpushonce
{{-- FIELD JS - will be loaded in the after_scripts section --}}
@pushonce('crud_fields_scripts')
    <script src="{{ asset('public/js/combodate.js') }}"></script>
    <script>
        jQuery(document).ready(function($){
            $('.month_combodate').combodate({
                minYear: 1970,
                maxYear: 2100
            });
        });
    </script>
@endpushonce