<!-- text input -->
@php
    $json_values = [];
    if(is_array($field['json_values'])) {
        $json_values = $field['json_values'];
    } else {
        $json_values = json_decode($field['json_values']);
    }
    $modules = \Module::whereIn('name',$json_values ?? [])->get();
    $name_type = $field['name'].'_type';
    $name_id = $field['name'].'_id';
    $value_type = old($name_type) ?? $crud->row->{$name_type} ?? ""; 
    $value_id = old($name_id) ?? $crud->row->{$name_id} ?? "";
    $field['attributes']['class'] = (isset($errors) && ($errors->has($name_type) || $errors->has($name_id))) ? $field['attributes']['class'].' is-invalid' : $field['attributes']['class'];
@endphp
@component(config('stlc.view_path.inc.input_group','stlc::inc.input_group'),['field' => $field])
    @slot('onInput')
        <div class="input-group f-select2-search" style="flex: 1 1 0%;">
            <select
                name="{{ $name_type }}"
                @include(config('stlc.view_path.inc.field_attributes','stlc::inc.field_attributes'),['class' => $field['attributes']['class'].' f-modal-select'])
                >
                @if(!(isset($field['attributes']['allows_null'])) || (isset($field['attributes']['allows_null']) && ($field['attributes']['allows_null'])))
                    <option value="">{{ 'Select '.str_replace('*','',strip_tags($field['label'])) }} type</option>
                @endif
                @foreach ($modules as $module)
                    <option value="{{ $module->model }}"
                        @if (isset($value_type) && $module->model==$value_type)
                            selected
                        @endif
                    >{{ $module->name }}</option>
                @endforeach
            </select>
            <select
                name="{{ $name_id }}"
                @include(config('stlc.view_path.inc.field_attributes','stlc::inc.field_attributes'),['class' => $field['attributes']['class'].' polymorphic_select2_ajax'])
                >
                @if(!(isset($field['attributes']['allows_null'])) || (isset($field['attributes']['allows_null']) && ($field['attributes']['allows_null'])))
                    <option value="">{{ 'Select '.str_replace('*','',strip_tags($field['label'])) }} id</option>
                @endif
                @if (isset($value_id))
                    @php
                        $value_module = \Module::where('model',$value_type)->first();
                        if(isset($value_module->model) && class_exists($value_module->model)) {
                            $value_data = $value_module->model::where('id',$value_id)->first();
                        }
                    @endphp
                    @if(isset($value_module->model) && class_exists($value_module->model))
                        <option value="{{ $value_id }}" selected>{{ \CustomHelper::get_represent_attr($value_data) }}</option>
                    @endif
                @endif
            </select>
        </div>
    @endslot
@endcomponent
@pushonce('crud_fields_styles')
    <link href="{{ asset('node_modules/admin-lte/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('node_modules/admin-lte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endpushonce
@pushonce('crud_fields_scripts')
    <script src="{{ asset('node_modules/admin-lte/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function($) {
            $('.f-modal-select').select2({
                theme:"bootstrap4"
            });
            $(".polymorphic_select2_ajax").each(function (i, obj) {
                if (!$(obj).hasClass("select2-hidden-accessible"))
                {   $(obj).select2({
                        theme:"bootstrap4",
                        minimumInputLength: "{{ $field['minimum_input_length'] ?? '0' }}",
                        ajax: {
                            url: "{{ url(config('stlc.stlc_route_prefix', 'developer').'/getModuleData') }}",
                            dataType: 'json',
                            type:'Post',
                            quietMillis: 250,
                            data: function (params) {
                                return {
                                    model:$(this).closest('.f-select2-search').find('.f-modal-select').first().val(),
                                    q: params.term, // search term
                                    page: params.page
                                };
                            },
                            error:function(data) {
                                console.log(data,'hello');
                                if(data.status == '422') {
                                    console.log($(obj).val(null).trigger("change"),obj);
                                }
                            },
                            processResults: function (data, params) {
                                console.log(data, params);
                                params.page = params.page || 1;
                                var result = {
                                    results: $.map(data.item, function (item) {
                                        return {text: item['text'],id: item["value"]}
                                    }),
                                    more: data.current_page < data.last_page
                                };
                                return result;
                            },
                            cache: true
                        },
                    });
                }
            });
            $('.f-modal-select').on('change',function(){
                $(this).closest('.f-select2-search').find('.polymorphic_select2_ajax').first().val(null).trigger("change");
            });
        });
    </script>
@endpushonce