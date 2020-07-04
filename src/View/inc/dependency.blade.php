@php
    $parent_label_name = isset($parent_label_name) ? $parent_label_name : 'Parent';
    $parent_input_name = isset($parent_input_name) ? $parent_input_name : 'parent_id';
    $child_label_name = isset($child_label_name) ? $child_label_name : 'child';
    $child_input_name = isset($child_input_name) ? $child_input_name : 'child_id';

    $parent_input_value = old($parent_input_name) ?? $parent_input_value ?? null;
    $child_input_value = old($child_input_name) ?? $child_input_value ?? null;

    $option_name_parent = isset($option_name_parent) ? $option_name_parent : 'Select '.$parent_label_name;
    $option_name_child = isset($option_name_child) ? $option_name_child : 'Select '.$child_label_name;

    $required_parent = isset($required_parent) ? $required_parent : '';
    $required_child = isset($required_child) ? $required_child : '';

    $parents = $parents ?? [];
    $child_data = $child_data ?? [];
@endphp
<div class="row dependency">
    <div class='col-lg-6 col-md-6 col-sm-6 col-xs-12'>
        <div class="form-group padd-location">
            <label for="{{$parent_input_name}}" class="control-label">{{$parent_label_name}} {!! ($required_parent == 'required') ? '<span style="color:red;">*</span>' : '' !!}</label>
            <select
                name="{{$parent_input_name}}"
                class="padd-location form-control selectparent-child {{$parent_input_name}}_parent_select"
                data-option_name="{{$option_name_parent}}"
                style="width:100%;"
                {{ $required_parent }}
            >
                <option value="">{{$option_name_parent}}</option>
                @foreach($parents as $parent)
                    <option
                        value="{{$parent->id}}"
                        @if(isset($parent_input_value) && !is_bool($parent_input_value) && $parent_input_value == $parent->id)
                            selected
                        @endif
                    >{{$parent->name}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class='col-lg-6 col-md-6 col-sm-6 col-xs-12'>
        <div class="form-group padd-location">
            <label for="{{$child_input_name}}" class="control-label">{{$child_label_name}} {!! ($required_child == 'required') ? '<span style="color:red;">*</span>' : '' !!}</label>
            <select
                name="{{$child_input_name}}"
                class="padd-location form-control selectparent-child {{$child_input_name}}_child_select"
                data-option_name="{{$option_name_child}}"
                input_value="{{$child_input_value ?? null}}"                
                {{ $required_child }}
                style="width:100%;"
            >
                <option value="">{{$option_name_child}}</option>
            </select>
        </div>
    </div>
</div>
@push('after_scripts')
    <script>
        $(document).ready(function () {
            $('.{{$parent_input_name}}_parent_select').each(function (index, element) {
                (function(that, i) { 
                    var t = setTimeout(function() { 
                        child{{$parent_input_name}}(element);
                    }, 500 * i);
                })(this, index);
                // $.when(child(element)).done();
            });

            $(':input[name="{{$parent_input_name}}"]').on('change',function(){
                child{{$parent_input_name}}(this);
            });
        });
        function child{{$parent_input_name}}(element) {
            $select_input = $(element);
            var data = {!! json_encode($child_data) !!};
            @if(isset($filter))
                var filter = JSON.stringify({!! json_encode($filter) !!});
                data['filter'] = [];
                data['filter'].push(JSON.parse(filter.replace("__value__",element.value)));
            @else
                data["filter"] = { "{{$attr_name ?? 'np'}}" : element.value};
            @endif
            var value = (typeof $(':input[name="{{$child_input_name}}"]').attr('input_value') !== 'undefined') ? $(':input[name="{{$child_input_name}}"]').attr('input_value') : null;
            var html = '<option value="">{{$option_name_child}}</option>';
            // console.log(data);
            $.ajax({
                type: "{{ $ajax_method ?? 'Get' }}",
                url: "{{ $child_url ?? '' }}",
                data: data,
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
                        $(':input[name="{{$child_input_name}}"]').html(html);
                    } else {
                        
                    }
                }
            });
        }
    </script>
@endpush