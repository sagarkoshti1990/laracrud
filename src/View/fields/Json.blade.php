<!-- text input -->
@php
    if(isset($field['attributes']['placeholder'])) {
        unset($field['attributes']['placeholder']);
    }
    if(count($field['options']) < 4 ) {
        $col = (12 / count($field['options']));
    } else {
        $col = '3';
        $field['attributes']['class'] = $field['attributes']['class'].' mb-10';
    }
@endphp
@component(config('stlc.view_path.inc.input_group','stlc::inc.input_group'),['field' => $field])
    @slot('onInput')
        <div class="row" style="flex: 1 1 0%;">
            @foreach ($field['options'] as $key => $value)
            @php
                $field['attributes']['class'] = (isset($errors) && $errors->has($field['name'].'_'.$value)) ? $field['attributes']['class'].' is-invalid' : $field['attributes']['class'];
            @endphp
            <div class="col-lg-{{$col}} col-md-{{$col}} col-sm-{{$col}} col-xs-12">
                <label for="{{ $value }}" class="control-label">{!! $value !!}</label>
                <div class="form-group">
                    <input
                        type="{{$field['attributes']['input_type']}}"
                        name="{{ $field['name'].'_'.$value }}"
                        value="{{ old($field['name'].'_'.$value) ? old($field['name'].'_'.$value) : ((old($field['name']) && isset(json_decode(old($field['name']))->$value)) ? json_decode(old($field['name']))->$value : (isset($field['value'], json_decode($field['value'])->$value) ? json_decode($field['value'])->$value : '' )) }}"
                        @include(config('stlc.view_path.inc.field_attributes','stlc::inc.field_attributes'))
                        placeholder="Enter {{ $value }}"
                    >
                </div>
            </div>
            @endforeach
        </div>
    @endslot
@endcomponent