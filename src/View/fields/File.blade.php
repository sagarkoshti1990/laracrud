@php
    $img = $hide = "";
    $value = old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' ));
    if(isset($value) && is_numeric($value) && $value) {
        $img = \CustomHelper::showHtml($value,'uploaded_file text-wrap my-1 mr-2 align-top');
        $hide = 'd-none';
    }
	$errorClass = (isset($errors) && $errors->has($field['name'])) ? 'form-control is-invalid' : "";
    $field['prefix'] = $field['prefix'] ?? '<span class="fa fa-cloud-upload-alt"></span>';
@endphp
@component(config('stlc.view_path.inc.input_group','stlc::inc.input_group'),['field' => $field,'f_input_group' => $hide])
    @slot('onInput')
    <input type="hidden" name="{{ $field['name'] }}" value="{{ $value ?? "" }}"
        @include(config('stlc.view_path.inc.field_attributes','stlc::inc.field_attributes'))
    >
    <a class="btn btn-default btn_upload_file btn-labeled {{ $hide }} {{ $errorClass }} input-group-text" file_type='file'
        ratio="{{ $field['attributes']['ratio'] ?? $field['ratio'] ?? '' }}"
        @if(isset($field['file_type'])) extension="{{ $field['file_type'] }}" @endif
        selecter="{{ $field['name'] }}">Upload</a>
    @endslot
    @slot('afterInput'){!! $img !!}@endslot
@endcomponent