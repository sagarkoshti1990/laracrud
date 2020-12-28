@component(config('stlc.view_path.inc.input_group','stlc::inc.input_group'),['field' => $field])
@slot('beforInput')
    @php
    if (isset($field['value']) && (is_array($field['value']) || is_object($field['value']))) {
        $field['value'] = json_encode($field['value']);
    }
    @endphp
    <input type="hidden" value="{{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' )) }}" name="{{ $field['name'] }}">
@endslot
@slot('onInput')
    @if(isset($field['store_as_json']) && $field['store_as_json'])
        <input
            type="text"
            data-address="{&quot;field&quot;: &quot;{{$field['name']}}&quot;, &quot;full&quot;: {{isset($field['store_as_json']) && $field['store_as_json'] ? 'true' : 'false'}} }"
            @include(config('stlc.view_path.inc.field_attributes','stlc::inc.field_attributes'))
        >
    @else
        <input
            type="text"
            data-address="{&quot;field&quot;: &quot;{{$field['name']}}&quot;, &quot;full&quot;: {{isset($field['store_as_json']) && $field['store_as_json'] ? 'true' : 'false'}} }"
            name="{{ $field['name'] }}"
            value="{{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' )) }}"
            @include(config('stlc.view_path.inc.field_attributes','stlc::inc.field_attributes'))
        >
    @endif
@endslot
@endcomponent
{{-- FIELD CSS - will be loaded in the after_styles section --}}
@pushonce('crud_fields_styles')
    <style>
        .ap-input-icon.ap-icon-pin {right: 5px !important; }
        .ap-input-icon.ap-icon-clear {right: 10px !important; }
        .algolia-places{flex: 1 1 0%;}
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