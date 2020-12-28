@php
    $field['attributes']['class'] = $field['attributes']['class']." d-none";
    $values = old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' ));
    $img = "<div class='uploaded_files'></div>";
    if(isset($values) && is_array($values) && count($values)) {
        $img = "<div class='uploaded_files'>";
        foreach ($values as $key => $value) {
            $img .= \CustomHelper::showHtml($value,'uploaded_file2 d-inline-block position-relative my-1 mr-2 align-top text-wrap');
        }
        $img .= "</div>";
    }
	$errorClass = (isset($errors) && $errors->has($field['name'])) ? 'form-control is-invalid h-100' : "";
    $field['prefix'] = $field['prefix'] ?? '<span class="fa fa-cloud-upload-alt"></span>';
@endphp
@component(config('stlc.view_path.inc.input_group','stlc::inc.input_group'),['field' => $field])
    @slot('onInput')
    <select name="{{ $field['name'] }}[]" @include(config('stlc.view_path.inc.field_attributes','stlc::inc.field_attributes')) multiple>
        @if (isset($values) && is_array($values) && count($values))
            @foreach ($values as $key => $value)
                <option value="{{ $value }}" selected>{{ $value }}</option>
            @endforeach
        @endif
    </select>
    <a class="btn btn-default btn_upload_file btn-labeled {{ $errorClass }} input-group-text" file_type='files'
        ratio="{{ $field['attributes']['ratio'] ?? $field['ratio'] ?? '' }}"
        image_public="{{ $field['attributes']['image_public'] ?? $field['image_public'] ?? '' }}"
        @if(isset($field['file_type'])) extension="{{ $field['file_type'] }}" @endif
        selecter="{{ $field['name'] }}">Upload</a>
    @endslot
    @slot('afterInput'){!! $img !!}@endslot
@endcomponent