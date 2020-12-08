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
    $field['attributes']['class'] = ($errors->has($name_type) || $errors->has($name_id)) ? $field['attributes']['class'].' is-invalid' : $field['attributes']['class'];
@endphp
<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
    @endif
    <div class="input-group f-select2-search">
        @if(isset($field['prefix'])) <div class="input-group-prepend"><span class="input-group-text">{!! $field['prefix'] !!}</span></div> @endif
        <select
            name="{{ $name_type }}"
            @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes',['class' => $field['attributes']['class'].' f-modal-select'])
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
            @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes',['class' => $field['attributes']['class'].' polymorphic_select2_ajax'])
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
                @if(isset($value_module->model) && class_exists($value_module->model) && isset($value_data->{$value_module->represent_attr}))
                    <option value="{{ $value_id }}" selected>{{ $value_data->{$value_module->represent_attr} }}</option>
                @endif
            @endif
        </select>
        @if(isset($field['suffix'])) <div class="input-group-append"><span class="input-group-text">{!! $field['suffix'] !!}</span></div> @endif
    </div>
    @if ($errors->has($name_type) || $errors->has($name_id))
        <div class="is-invalid"></div>
        <span class="invalid-feedback">{{ $errors->first($name_type) }} {{ $errors->first($name_id)  }}</span>
    @endif
    @if (isset($field['hint'])){{-- HINT --}}
        <p class="form-text">{!! $field['hint'] !!}</p>
    @endif
</div>
@pushonce('crud_fields_scripts')
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
                                    results: $.map(data.item.data, function (item) {
                                        console.log(item);
                                        return {
                                            text: item['text'],
                                            id: item["value"]
                                        }
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