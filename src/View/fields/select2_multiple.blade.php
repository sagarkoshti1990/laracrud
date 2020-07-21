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
            @elseif(method_exists($field['model'],'get_module') && $field['model']::get_module()->name == "Roles")
                @php $selec_list = $field['model']::get_all_admin_role(); @endphp
            @elseif(method_exists($field['model'],'get_module') && in_array( $field['model']::get_module()->name , ["Users","Employees",'MasterUsers']))
                @php
                    if($field['model']::get_module()->name == 'Users') {
                        $selec_list = $field['model']::whereHas('roles', function ($query) {
                            return $query->whereNotIn('roles.name', ['Super_admin']);
                        })->get();
                    } else {
                        $selec_list = $field['model']::get();
                    }
                    $field['attribute'] = ['title','first_name','last_name'];
                @endphp
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