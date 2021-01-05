<!-- number input -->
@php 
    $field['attributes']['class'] = $field['attributes']['class'].' phone_input';
    $code = $field['attributes']['code'] ?? null;
    $allowDropdown = $field['attributes']['allowDropdown'] ?? true;
@endphp
@component(config('stlc.view_path.inc.input_group','stlc::inc.input_group'),['field' => $field])
    @slot('onInput')
        <input
            type="tel"
            name="{{ $field['name'] }}"
            value="{{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) && $field['default'] != '' ? $field['default'] : '' )) }}"
            @include(config('stlc.view_path.inc.field_attributes','stlc::inc.field_attributes'))
        >
        <input type="hidden" name="{{ $code }}"
            value="{{ old($code) ? old($code) : (isset($crud->row->{$code}) ? $crud->row->{$code} : "91") }}"
        >
    @endslot
@endcomponent
@pushonce('after_styles')
<link rel="stylesheet" href="{{ asset('node_modules/intl-tel-input/build/css/intlTelInput.min.css') }}">
<style>
    .iti,.input-group, .input-group > .intl-tel-input.allow-dropdown{width: 100% !important;}
    .intl-tel-input .country-list{z-index: 5;}
    .intl-tel-input {color: #333;}
    .iti.iti--separate-dial-code{display:block;flex: 1 1 0%;}
</style>
@endpushonce
{{-- FIELD JS - will be loaded in the after_scripts section --}}
@pushonce('crud_fields_scripts')
    <!-- country code JavaScript -->
    <script src="{{ asset('node_modules/intl-tel-input/build/js/intlTelInput.js') }}"></script> 
    <!-- include phone js-->
    <script>
        $(document).ready(function($) {
            var input = document.querySelector(".phone_input");
            var iti = window.intlTelInput(input,{
                // hiddenInput:"{{ $code ?? 'code' }}",
                // utilsScript:"{{ asset('node_modules/intl-tel-input/build/js/utils.js') }}",
                // separateDialCode:true,
                allowDropdown:{{ $allowDropdown }},
                placeholderNumberType:"MOBILE",
                preferredCountries:['In']
            });
            $('.modal').on('shown.bs.modal', function(){
                let phone = $(input), lPadd = phone.prev('.iti__flag-container').width() + 6;
                phone.css('padding-left', lPadd);
            });
            input.addEventListener("countrychange", function(e) {
                $code_input = $(this).parents('.f-form-group').find(':input[name={{ $code ?? 'code' }}]').first();
                dialCode = iti.getSelectedCountryData();
                $code_input.val(dialCode.dialCode);
            });
            var data = window.intlTelInputGlobals.getCountryData().filter(function(X,y){
                return X.dialCode == "{{ old($code) ? old($code) : (isset($crud->row->{$code}) ? $crud->row->{$code} : "91") }}";
            });
            if(isset(data[0].iso2)) {
                iti.setCountry(data[0].iso2);
            }
        });
    </script>
@endpushonce