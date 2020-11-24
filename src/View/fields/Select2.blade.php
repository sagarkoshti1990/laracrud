<!-- select2 -->
<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
    @endif
    @if(isset($field['attributes']['quickadd']) && $field['attributes']['quickadd'] && (isset($field['model'])))
    <div class="row">
        <div class="col-md-10 pr0 col-sm-10 col-xs-10" style="width: 85% !important">
    @endif
    <select
        name="{{ $field['name'] }}"
        style="width: 100%"
        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes', ['default_class' =>  'form-control select2_field select2-purple'])
        >
        @if(!(isset($field['attributes']['allows_null'])) || (isset($field['attributes']['allows_null']) && ($field['attributes']['allows_null'])))
            <option value="">{{ (isset($field['attributes']['select_option']) && $field['attributes']['select_option'] == false) ? '' : "Select" }} {{ str_replace('*','',strip_tags($field['label'])) }}</option>
        @endif
        @if (isset($field['model']))
            @if(isset($field['model']) && is_object($field['model']))
                @php $selec_list = $field['model']->get(); @endphp
            @else
                @php $selec_list = $field['model']::get(); @endphp
            @endif
            @foreach ($selec_list as $connected_entity_item)
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
    @if(isset($field['attributes']['quickadd']) && $field['attributes']['quickadd'] && (isset($field['model'])))
        </div>
            <div class="col-md-2 col-sm-2 col-xs-2" style="width: 15% !important">
                <button type="button"
                    class="btn btn-default btn-xs float-right quick_add_modal"
                    data-filedname="{{ $field['name'] }}" data-modal="{{ $field['model'] }}"
                    style="font-size:15px;padding:3px 8px;"
                ><i class="fa fa-plus"></i></button>
            </div>
        </div>
    @endif
    @if ($errors->has($field['name']))
        <div class="is-invalid"></div><span class="invalid-feedback">{{ $errors->first($field['name']) }}</span>
    @endif
    @if (isset($field['hint'])){{-- HINT --}}
        <p class="form-text">{!! $field['hint'] !!}</p>
    @endif
</div>
{{-- FIELD JS - will be loaded in the after_scripts section --}}
@pushonce('crud_fields_styles')
    <link href="{{ asset('node_modules/admin-lte/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('node_modules/admin-lte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endpushonce
@pushonce('crud_fields_scripts')
    <!-- include select2 js-->
    <script src="{{ asset('node_modules/admin-lte/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        function readonly_select(objs, action) {
            if (action===true) {
                objs.prepend('<div class="disabled-select"></div>');
            } else {
                $(".disabled-select", objs).remove();
            }
        }
        jQuery(document).ready(function($) {
            // trigger select2 for each untriggered select2 box
            $('.select2_field').each(function (i, obj) {
                if (!$(obj).hasClass("select2-hidden-accessible"))
                {
                    $(obj).select2({
                        theme:"bootstrap4"
                    });
                    
                    if(isset($(this).attr('readonly')) && $(this).attr('readonly') == "readonly"){
                        // console.log($(this).attr('readonly'));
                        readonly_select($(this).parents('.form-group').find('span.select2'), true);
                    }
                }
            });
            readonly_select($(this).parents('.form-group').find('span.select2'), true);
        });
    </script>
@endpushonce