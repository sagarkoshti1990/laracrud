<!-- select -->

<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
    @endif
    <select
        name="{{ $field['name'] }}"
        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
        >
        @if(!(isset($field['attributes']['allows_null'])) || (isset($field['attributes']['allows_null']) && ($field['attributes']['allows_null'])))
            <option value="">{{ (isset($field['attributes']['select_option']) && $field['attributes']['select_option'] == false) ? '' : "Select" }} {{ str_replace('*','',strip_tags($field['label'])) }}</option>
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
        <div class="is-invalid"></div><span class="invalid-feedback">{{ $errors->first($field['name']) }}</span>
    @endif
    @if (isset($field['hint'])){{-- HINT --}}
        <p class="form-text">{!! $field['hint'] !!}</p>
    @endif
</div>