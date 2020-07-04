<!-- text input -->
@php
if (isset($field['value']) && (is_array($field['value']) || is_object($field['value']))) {
    $field['value'] = json_encode($field['value']);
}
@endphp
<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    <label>{!! $field['label'] !!}</label>
    @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_translatable_icon')
    <input type="hidden" value="{{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' )) }}" name="{{ $field['name'] }}">
    @if(isset($field['prefix']) || isset($field['suffix'])) <div class="input-group"> @endif
        @if(isset($field['prefix'])) <div class="input-group-addon">{!! $field['prefix'] !!}</div> @endif
        @if(isset($field['store_as_json']) && $field['store_as_json'])
        <input
            type="text"
            data-address="{&quot;field&quot;: &quot;{{$field['name']}}&quot;, &quot;full&quot;: {{isset($field['store_as_json']) && $field['store_as_json'] ? 'true' : 'false'}} }"
            @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
        >
        @else
        <input
            type="text"
            data-address="{&quot;field&quot;: &quot;{{$field['name']}}&quot;, &quot;full&quot;: {{isset($field['store_as_json']) && $field['store_as_json'] ? 'true' : 'false'}} }"
            name="{{ $field['name'] }}"
            value="{{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' )) }}"
            @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
        >
        @endif
        @if(isset($field['suffix'])) <div class="input-group-addon">{!! $field['suffix'] !!}</div> @endif
    @if(isset($field['prefix']) || isset($field['suffix'])) </div> @endif
    @if ($errors->has($field['name']))
        <span class="help-block">{{ $errors->first($field['name']) }}</span>
    @endif
    @if (isset($field['hint'])){{-- HINT --}}
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>
{{-- FIELD CSS - will be loaded in the after_styles section --}}
@pushonce('crud_fields_styles')
    <style>
        .ap-input-icon.ap-icon-pin {
            right: 5px !important; }
        .ap-input-icon.ap-icon-clear {
            right: 10px !important; }
    </style>
@endpushonce
{{-- FIELD JS - will be loaded in the after_scripts section --}}
@pushonce('crud_fields_scripts')
<script src="https://cdn.jsdelivr.net/npm/places.js@1.7.3"></script>
<script>
    jQuery(document).ready(function($){
        window.AlgoliaPlaces = window.AlgoliaPlaces || {};
        $('[data-address]').each(function(){
            var $this      = $(this),
            $addressConfig = $this.data('address'),
            $field = $('[name="'+$addressConfig.field+'"]'),
            $place = places({
                container: $this[0]
            });
            function clearInput() {
                if( !$this.val().length ){
                    $field.val('');
                }
            }
            if( $addressConfig.full ){
                $place.on('change', function(e){
                    var result = JSON.parse(JSON.stringify(e.suggestion));
                    delete(result.highlight); delete(result.hit); delete(result.hitIndex);
                    delete(result.rawAnswer); delete(result.query);
                    $field.val( JSON.stringify(result) );
                });
                $this.on('change blur', clearInput);
                $place.on('clear', clearInput);
                if( $field.val().length ){
                    var existingData = JSON.parse($field.val());
                    $this.val(existingData.value);
                }
            }
            window.AlgoliaPlaces[ $addressConfig.field ] = $place;
        });
    });
</script>
@endpushonce