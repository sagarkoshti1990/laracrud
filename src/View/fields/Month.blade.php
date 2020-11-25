@php
    $value = isset($field['default']) ? $field['default'] : '';
    if(old($field['name'])) {
        try {
            $value = \Carbon::parse(old($field['name']))->format('m-Y');
        } catch (\Exception $e) {
            $value = old($field['name']);
        }
    } else if (isset($field['value'])) {
        $value = \Carbon::parse($field['value'])->format('m-Y');
    }
@endphp
<!-- html5 month input -->
<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
    @endif
    <input
        type="text"
        name="{{ $field['name'] }}"
        value="{{ $value }}"
        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
        data-format="MM-YYYY"
        data-template="MMM YYYY"
        data-width = "49%";
    >
    @if ($errors->has($field['name']))
        <div class="is-invalid"></div><span class="invalid-feedback">{{ $errors->first($field['name']) }}</span>
    @endif
    @if (isset($field['hint'])){{-- HINT --}}
        <p class="form-text">{!! $field['hint'] !!}</p>
    @endif
</div>
@pushonce('crud_fields_styles')
<style>
    .combodate{display: block;}
    .combodate select{display: inline-block;}
</style>
@endpushonce
{{-- FIELD JS - will be loaded in the after_scripts section --}}
@pushonce('crud_fields_scripts')
    <script src="{{ asset('public/js/combodate.js') }}"></script>
    <script>
        jQuery(document).ready(function($){
            $('.month_combodate').combodate({
                customClass:'{{ $errors->has($field['name']) ? "form-control is-invalid" : "form-control" }}',
                minYear: 1970,
                maxYear: 2100
            });
        });
    </script>
@endpushonce