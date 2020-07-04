<!-- text input -->
<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_translatable_icon')
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
        @if(isset($field['prefix'])) <div class="input-group-addon">{!! $field['prefix'] !!}</div> @endif
            <div class="row">
                @foreach ($field['options'] as $key => $value)
                <div class="col-lg-{{$col}} col-md-{{$col}} col-sm-{{$col}} col-xs-12">
                    <label for="{{ $value }}" class="control-label">{!! $value !!}</label>
                    <div class="form-group">
                        <input
                            type="{{$field['attributes']['input_type']}}"
                            name="{{ $field['name'].'_'.$value }}"
                            value="{{ old($field['name']) ? old($field['name']) : (isset($field['value'], json_decode($field['value'])->$value) ? json_decode($field['value'])->$value : (isset($field['default']) ? $field['default'] : '' )) }}"
                            @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
                            placeholder="Enter {{ $value }}"
                        >
                    </div>
                </div>
                @endforeach
            </div>
        @if(isset($field['suffix'])) <div class="input-group-addon">{!! $field['suffix'] !!}</div> @endif
    @if(isset($field['prefix']) || isset($field['suffix'])) </div> @endif
    @if ($errors->has($field['name']))
        <span class="help-block">{{ $errors->first($field['name']) }}</span>
    @endif
    @if (isset($field['hint'])){{-- HINT --}}
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>