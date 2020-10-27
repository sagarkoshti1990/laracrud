<!-- select multiple -->
<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_translatable_icon')
    @endif
    <select
    	class="form-control"
        name="{{ $field['name'] }}[]"
        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
    	multiple>

		@if (!isset($field['allows_null']) || $field['allows_null'])
            <option value="">None</option>
		@endif

        @if (isset($field['model']))
            @foreach ($field['model']::all() as $connected_entity_item)
                <option value="{{ $connected_entity_item->getKey() }}"
                    @if ( (isset($field['value']) && in_array($connected_entity_item->getKey(), $field['value']->pluck($connected_entity_item->getKeyName(), $connected_entity_item->getKeyName())->toArray())) || ( old( $field["name"] ) && in_array($connected_entity_item->getKey(), old( $field["name"])) ) )
                        selected
                    @endif
                >{{ $connected_entity_item->{$field['attribute']} }}</option>
            @endforeach
        @elseif (isset($field['options']) && is_array($field['options']) && count($field['options']))
            @foreach ($field['options'] as $key => $value)
                <option value="{{ $key }}"
                    @if (isset($field['value']) && (is_array(json_decode($field['value'])) && in_array($key, json_decode($field['value'])))
                        || ( ! is_null( old($field['name']) ) && old($field['name']) == $key))
                        selected
                    @endif
                >{{ $value }}</option>
            @endforeach
        @endif
	</select>
    @if ($errors->has($field['name']))
        <span class="help-block">{{ $errors->first($field['name']) }}</span>
    @endif
    @if (isset($field['hint'])){{-- HINT --}}
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>