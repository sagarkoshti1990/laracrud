<!-- text input -->
@php
    $json_values = [];
    if(is_array($field['json_values'])) {
        $json_values = $field['json_values'];
    } else {
        $json_values = json_decode($field['json_values']);
    }
    $modules = \Module::whereIn('name',$json_values)->get();
    $name_type = $field['name'].'_type';
    $name_id = $field['name'].'_id';
    $value_type = $crud->row->{$name_type} ?? ""; 
    $value_id = $crud->row->{$name_id} ?? "";
@endphp
<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_translatable_icon')
    @endif
    <div class="input-group">
        @if(isset($field['prefix'])) <div class="input-group-addon">{!! $field['prefix'] !!}</div> @endif
        <select
            name="{{ $name_type }}"
            @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
            >
            @if(!(isset($field['attributes']['allows_null'])) || (isset($field['attributes']['allows_null']) && ($field['attributes']['allows_null'])))
                <option value="">{{ 'Select '.str_replace('*','',strip_tags($field['label'])) }} type</option>
            @endif
            @foreach ($modules as $module)
                <option value="{{ $module->model }}"
                    @if ( ( old($field['name']) && old($field['name']) == $module->model ) || (isset($value_type) && $module->model==$value_type))
                        selected
                    @endif
                >{{ $module->name }}</option>
            @endforeach
        </select>
        <select
            name="{{ $name_id }}"
            @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
            >
            @if(!(isset($field['attributes']['allows_null'])) || (isset($field['attributes']['allows_null']) && ($field['attributes']['allows_null'])))
                <option value="">{{ 'Select '.str_replace('*','',strip_tags($field['label'])) }} id</option>
            @endif
        </select>
        @if(isset($field['suffix'])) <div class="input-group-addon">{!! $field['suffix'] !!}</div> @endif
    </div>
    @if ($errors->has($field['name']))
        <span class="help-block">{{ $errors->first($field['name']) }}</span>
    @endif
    @if (isset($field['hint'])){{-- HINT --}}
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>

@pushonce('crud_fields_scripts')
    <script>
        $(document).ready(function($) {
            $(':input[name="{{$name_type}}"]').on('change',function(){
                var model = $(this).val();
                var child = $(this).parent().find(`:input:not([name=${this.name}])`).attr('disabled',true);
                var value = "{{ $value_id }}";
                var html = `<option value="">{{ 'Select '.str_replace('*','',strip_tags($field['label'])) }} id</option>`;
                $.ajax({
                    type: "Post",
                    url: "{{ url(config('lara.base.route_prefix', 'admin').'/getModuleData') }}",
                    data: {model:model},
                    success: function (data) {
                        // console.log(data);
                        if(data.status == 'success' || data.statusCode == '200') {
                            for (var i = 0; i < data.data.length; i++) {
                                var item = data.data[i];
                                var selected = "";
                                if(isset(value) && value != "" && item.id == value) {
                                    selected = "selected";
                                }
                                html += '<option value="' + item.id + '" '+selected+'>' + item.name + '</option>';
                            }
                            $(child).html(html).attr('disabled',false);
                        } else {
                            
                        }
                    }
                });
            });

            @if($value_type != "") 
                $(':input[name="{{$name_type}}"]').trigger('change');
            @endif
        });
    </script>
@endpushonce