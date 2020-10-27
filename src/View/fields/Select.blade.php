<!-- select -->

<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_translatable_icon')
    @endif
    <select
        name="{{ $field['name'] }}"
        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
        >
        @if (isset($field['allows_null']) && $field['allows_null']==true)
            <option value="">None</option>
        @endif

        @if (isset($field['model']))
            @foreach ($field['model']::all() as $connected_entity_item)
                @php
                    $option_text = "";
                    if(is_array($field['attribute'])) {
                        $attributes = collect($field['attribute'])->except(['implode'])->all();
                        foreach($attributes as $key => $value) {
                            $option_text .= $connected_entity_item->{$value};
                            if(($key != count($attributes)-1)) {
                                if(isset($field['attribute']['implode'])) {
                                    $option_text .= $field['attribute']['implode'];
                                } else {
                                    $option_text .= " ";
                                }
                            }
                        }
                        $option_text = trim($option_text);
                    } else {
                        $option_text = $connected_entity_item->{$field['attribute']};
                    }
                @endphp
                <option value="{{ $connected_entity_item->getKey() }}"
                    @if ( ( old($field['name']) && old($field['name']) == $connected_entity_item->getKey() ) || (isset($field['value']) && $connected_entity_item->getKey()==$field['value']))
                        selected
                    @endif
                >{{ $option_text }}</option>
            @endforeach
        @elseif (isset($field['options']) && is_array($field['options']) && count($field['options']))
            @foreach ($field['options'] as $key => $value)
                <option value="{{ $key }}"
                    @if (isset($field['value']) && ($key==$field['value'] || (is_array($field['value']) && in_array($key, $field['value'])))
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