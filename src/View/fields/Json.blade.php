<!-- text input -->
<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
    @endif
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
    @if(isset($field['prefix']) || isset($field['suffix'])) <div class="input-group"> @endif
        @if(isset($field['prefix'])) <div class="input-group-prepend"><span class="input-group-text">{!! $field['prefix'] !!}</span></div> @endif
            <div class="row">
                @foreach ($field['options'] as $key => $value)
                @php
                    $field['attributes']['class'] = $errors->has($field['name'].'_'.$value) ? $field['attributes']['class'].' is-invalid' : $field['attributes']['class'];
                @endphp
                <div class="col-lg-{{$col}} col-md-{{$col}} col-sm-{{$col}} col-xs-12">
                    <label for="{{ $value }}" class="control-label">{!! $value !!}</label>
                    <div class="form-group">
                        <input
                            type="{{$field['attributes']['input_type']}}"
                            name="{{ $field['name'].'_'.$value }}"
                            value="{{ old($field['name'].'_'.$value) ? old($field['name'].'_'.$value) : (old($field['name']) && isset(json_decode(old($field['name']))->$value)) ? json_decode(old($field['name']))->$value : (isset($field['value'], json_decode($field['value'])->$value) ? json_decode($field['value'])->$value : (isset($field['default']) ? $field['default'] : '' )) }}"
                            @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
                            placeholder="Enter {{ $value }}"
                        >
                    </div>
                </div>
                @endforeach
            </div>
        @if(isset($field['suffix'])) <div class="input-group-append"><span class="input-group-text">{!! $field['suffix'] !!}</span></div> @endif
    @if(isset($field['prefix']) || isset($field['suffix'])) </div> @endif
    @if ($errors->has($field['name']))
        <div class="is-invalid"></div><span class="invalid-feedback">{{ $errors->first($field['name']) }}</span>
    @endif
    @if (isset($field['hint'])){{-- HINT --}}
        <p class="form-text">{!! $field['hint'] !!}</p>
    @endif
</div>