<!-- input group -->
@php
    // $field['prefix'] = $field['prefix'] ?? 'pre';$field['suffix'] = $field['suffix'] ?? 'suffi';
@endphp
<div @include(config('stlc.view_path.inc.field_wrapper_attributes','stlc::inc.field_wrapper_attributes'),['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
    @endif
    {{ $beforInput ?? "" }}
    @if(isset($field['prefix']) || isset($field['suffix'])) <div class="input-group f-input-group {{ $f_input_group ?? '' }}"> @endif
    @if(isset($field['prefix'])) <div class="input-group-prepend"><span class="input-group-text">{!! $field['prefix'] !!}</span></div> @endif
    {{ $onInput ?? "" }}
    @if(isset($field['suffix'])) <div class="input-group-append"><span class="input-group-text">{!! $field['suffix'] !!}</span></div> @endif
    @if(isset($field['prefix']) || isset($field['suffix'])) </div> @endif
    @if (isset($errors) && $errors->has($field['name']))
        <div class="is-invalid"></div><span class="invalid-feedback">{{ $errors->first($field['name']) }}</span>
    @endif
    {{ $afterInput ?? "" }}
    @if (isset($field['hint'])){{-- HINT --}}
        <p class="form-text">{!! $field['hint'] !!}</p>
    @endif
</div>