<!-- select2 multiple -->
<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_translatable_icon')
    @endif
    <select
        name="{{ $field['name'] }}[]"
        style="width: 100%"
        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
        multiple>

        @if (isset($field['model']))
            @if(isset($field['model']) && is_object($field['model']))
                @php $selec_list = $field['model']->get(); @endphp
            @else
                @php $selec_list = $field['model']::get(); @endphp
            @endif
            @foreach ($selec_list as $connected_entity_entry)
                @php
                    $option_text = "";
                    if(is_array($field['attribute'])) {
                        $attributes = collect($field['attribute'])->except(['implode'])->all();
                        foreach($attributes as $key => $value) {
                            $option_text .= $connected_entity_entry->{$value};
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
                        $option_text = $connected_entity_entry->{$field['attribute']};
                    }
                @endphp
                <option value="{{ $connected_entity_entry->getKey() }}"
                    @if( (isset($field['value'])) && in_array($connected_entity_entry->getKey(), collect(json_decode($field['value']))->toArray()) || ( old( $field["name"]) && is_array(old( $field["name"])) && in_array($connected_entity_entry->getKey(), old( $field["name"])) ) )
                        selected
                    @endif
                >{{ $option_text }}</option>
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
{{-- FIELD CSS - will be loaded in the after_styles section --}}
@pushonce('crud_fields_styles')
    <!-- include select2 css-->
    <link href="{{ asset('node_modules/admin-lte/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    {{-- <link href="{{ asset('public/vendor/select2/css/select2-bootstrap.css') }}" rel="stylesheet" type="text/css" /> --}}
    <style>
        .select2-results__options[aria-multiselectable="true"] .select2-results__option[aria-selected=true] {
            display: none;
        }
        .select2-container--default .select2-selection--single{
            display: block;width: 100%;height: calc(1.5em + 0.75rem + 2px);
            padding: 0.375rem 0.75rem;border: 1px solid #ced4da;border-radius: 0;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered{line-height:2;padding:0}
        .select2-container--default .select2-selection--single .select2-selection__arrow{height:36px}
        select[readonly].select2 + .select2-container {
            pointer-events: none;touch-action: none;
            .select2-selection {background: #eee;box-shadow: none;}
            .select2-selection__arrow,.select2-selection__clear {display: none;}
        }
        .disabled-select {background-color:#e5e9ed;opacity:0.5;border-radius:3px;cursor:not-allowed;
            position:absolute;top:0;bottom:0;right:0;left:0;
        }
        .has-error .select2-dropdown, .has-error .select2-selection{
            border-color: #f55753 !important;
        }
    </style>
@endpushonce
{{-- FIELD JS - will be loaded in the after_scripts section --}}
@pushonce('crud_fields_scripts')
    <!-- include select2 js-->
    <script src="{{ asset('node_modules/admin-lte/bower_components/select2/dist/js/select2.min.js') }}"></script>
    <script>
        jQuery(document).ready(function($) {
            // trigger select2 for each untriggered select2_multiple box
            $('.select2_multiple').each(function (i, obj) {
                if (!$(obj).hasClass("select2-hidden-accessible"))
                {
                    $(obj).select2({
                        placeholder: $(this).attr('placeholder')
                    });
                }
            });
        });
    </script>
@endpushonce