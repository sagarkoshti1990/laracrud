<!-- number input -->
<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label" style="display:block;">{!! $field['attributes']['label'] ?? $field['label'] !!}</label>
        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_translatable_icon')
    @endif
    @php 
        $field['attributes']['class'] = $field['attributes']['class'].' phone_input';
        $code = $field['attributes']['code'] ?? null;
        $allowDropdown = $field['attributes']['allowDropdown'] ?? true;
    @endphp
    @if(isset($field['prefix']) || isset($field['suffix'])) <div class="input-group"> @endif
        @if(isset($field['prefix'])) <div class="input-group-prepend"><span class="input-group-text">{!! $field['prefix'] !!}<span></div> @endif
        <input
            type="tel"
            name="{{ $field['name'] }}"
            value="{{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) && $field['default'] != '' ? $field['default'] : '' )) }}"
            @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
        >
        @if(isset($code))
            <input type="hidden" name="{{$code}}" value="91">
        @endif
        @if(isset($field['suffix'])) <div class="input-group-append"><span class="input-group-text">{!! $field['suffix'] !!}<span></div> @endif
    @if(isset($field['prefix']) || isset($field['suffix'])) </div> @endif
    @if ($errors->has($field['name']))
        <span class="help-block">{{ $errors->first($field['name']) }}</span>
    @endif
    @if (isset($field['hint'])){{-- HINT --}}
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>
@pushonce('after_styles')
<link rel="stylesheet" href="{{ asset('node_modules/intl-tel-input/build/css/intlTelInput.min.css') }}">
<style>
    .input-group, .input-group > .intl-tel-input.allow-dropdown{
        width: 100% !important;
    }
    .intl-tel-input .country-list{
        z-index: 5;
    }
    .intl-tel-input {
        color: #333;
    }
    .iti.iti--separate-dial-code{
        display:block;
    }
</style>
@endpushonce
{{-- FIELD JS - will be loaded in the after_scripts section --}}
@pushonce('crud_fields_scripts')
    <!-- country code JavaScript -->
    <script src="{{ asset('node_modules/intl-tel-input/build/js/intlTelInput.js') }}"></script> 
    <!-- include phone js-->
    <script>
        jQuery(document).ready(function($) {
            var input = document.querySelector(".phone_input");
            var iti = window.intlTelInput(input,{
                customPlaceholder: 'polite',
                separateDialCode:true,
                allowDropdown:{{ $allowDropdown }},
                placeholderNumberType:"MOBILE",
                preferredCountries:['In'],
                geoIpLookup: function(callback) {
                    callback("In");
                    // $.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                    //     var countryCode = (resp && resp.country) ? resp.country : "";
                    //     callback(countryCode);
                    // });
                }
            });
            @if(isset($code))
                input.addEventListener("countrychange", function(e) {
                    $code_input = $(this).parents('.form-group').find(':input[type=hidden]').first();
                    dialCode = iti.getSelectedCountryData();
                    $code_input.val(dialCode.dialCode);
                });
            @endif
        });
    </script>
@endpushonce