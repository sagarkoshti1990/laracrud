<!-- select2 -->
<div @include('crud.inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
        @include('crud.inc.field_translatable_icon')
    @endif
    @if(isset($field['attributes']['quickadd']) && $field['attributes']['quickadd'] && (isset($field['model'])))
    <div class="row">
        <div class="col-md-10 pr0 col-sm-10 col-xs-10" style="width: 85% !important">
    @endif
    <select
        name="{{ $field['name'] }}"
        style="width: 100%"
        @include('crud.inc.field_attributes', ['default_class' =>  'form-control select2_field'])
        >
        @if(!(isset($field['attributes']['allows_null'])) || (isset($field['attributes']['allows_null']) && ($field['attributes']['allows_null'])))
            <option value="">{{ 'Select '.str_replace('*','',strip_tags($field['label'])) }}</option>
        @endif

        @if (isset($field['allows_null']) && $field['allows_null'] == true)
            {{-- @if (true || $crud->isColumnNullable($field['name'])) --}}
                {{-- <option value="">{{ 'select '.$field['label'] }}</option> --}}
            {{-- @endif --}}
        @endif
            @if (isset($field['model']))
                @if(method_exists($field['model'],'get_module') && $field['model']::get_module()->name == "Roles")
                    @php $selec_list = $field['model']::get_all_admin_role(); @endphp
                @elseif(method_exists($field['model'],'get_module') && in_array( $field['model']::get_module()->name , ["Employees","PartnerUsers",'MasterUsers']))
                    @php $selec_list = $field['model']::get(); $field['attribute'] = ['first_name','last_name'];@endphp
                @elseif(method_exists($field['model'],'get_module') && in_array($field['model']::get_module()->name, ["Departments"]))
                    @php $selec_list = $field['model']::where('status', '!=', 'Deactive')->get(); @endphp
                @else
                    @php $selec_list = $field['model']::all(); @endphp
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
                        @if ( ( old($field['name']) && old($field['name']) == $connected_entity_entry->getKey() ) || (isset($field['value']) && $connected_entity_entry->getKey()==$field['value']))
                            selected
                        @endif
                    >{{ $option_text }}</option>
                @endforeach
            @elseif (count($field['options']))
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
                    class="btn btn-default btn-xs pull-right quick_add_modal"
                    data-filedname="{{ $field['name'] }}" data-modal="{{ $field['model'] }}"
                    style="font-size:15px;padding:3px 8px;"
                ><i class="fa fa-plus"></i></button>
            </div>
        </div>
    @endif

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
    {{-- FIELD CSS - will be loaded in the after_styles section --}}
    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_styles')
        <link href="{{ asset('node_modules/admin-lte/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    @endpush
    @push('crud_fields_scripts')
        <!-- include select2 css-->
        {{-- <link href="{{ asset('public/vendor/select2/css/select2-bootstrap.css') }}" rel="stylesheet" type="text/css" /> --}}
        <style>
            select[readonly].select2 + .select2-container {
                pointer-events: none;
                touch-action: none;

                .select2-selection {
                    background: #eee;
                    box-shadow: none;
                }

                .select2-selection__arrow,
                .select2-selection__clear {
                    display: none;
                }
            }
            .disabled-select {
                background-color:#e5e9ed;
                opacity:0.5;
                border-radius:3px;
                cursor:not-allowed;
                position:absolute;
                top:0;
                bottom:0;
                right:0;
                left:0;
            }
            .has-error .select2-dropdown, .has-error .select2-selection{
                border-color: #f55753 !important;
            }
        </style>
        <!-- include select2 js-->
        <script src="{{ asset('node_modules/admin-lte/bower_components/select2/dist/js/select2.min.js') }}"></script>
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
                        $(obj).select2();
                        
                        if(isset($(this).attr('readonly')) && $(this).attr('readonly') == "readonly"){
                            // console.log($(this).attr('readonly'));
                            readonly_select($(this).parents('.form-group').find('span.select2'), true);
                        }
                    }
                });
                readonly_select($(this).parents('.form-group').find('span.select2'), true);
            });
        </script>
    @endpush
@endif
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}