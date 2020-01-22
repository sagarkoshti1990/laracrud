<!-- number input -->
<div @include('crud.inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label" style="display:block;">{!! $field['attributes']['label'] ?? $field['label'] !!}</label>
        @include('crud.inc.field_translatable_icon')
    @endif
    @php 
        $field['attributes']['class'] = 'form-control phone_input';
    @endphp
        <input
        	type="tel"
        	name="{{ $field['name'] }}"
            id="{{ $field['name'] }}"
            value="{{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) && $field['default'] != '' ? $field['default'] : '' )) }}"
            @include('crud.inc.field_attributes')
        	>
        @if(isset($field['suffix'])) <div class="input-group-addon">{!! $field['suffix'] !!}</div> @endif
    
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
{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}
@if ($crud->checkIfOnce($field))

@push('after_styles')
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
@endpush
{{-- FIELD JS - will be loaded in the after_scripts section --}}
@push('crud_fields_scripts')
    <!-- country code JavaScript -->
    <script src="{{ asset('node_modules/intl-tel-input/build/js/intlTelInput.js') }}"></script> 
    <!-- include phone js-->
    <script>
        jQuery(document).ready(function($) {
            var input = document.querySelector(".phone_input");
            window.intlTelInput(input,{
                customPlaceholder: 'polite',
                separateDialCode:true,
                allowDropdown:false,
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
        });
    </script>
@endpush
@endif
