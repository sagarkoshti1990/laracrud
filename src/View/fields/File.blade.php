@php
    $img = $hide = "";
    $value = old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' ));
    if(isset($value) && is_numeric($value) && $value) {
        $img = CustomHelper::showHtml($value);
        $hide = 'd-none';
    }
	$errorClass = $errors->has($field['name']) ? 'form-control is-invalid' : "";
@endphp
<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <div>
            <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!} @if(isset($field['file_type'])) <span style="color:red"> (Only {{ $field['file_type'] }})</span> @endif</label>
        </div>
    @endif
    <div class="btn-group">
        <input
            type="hidden"
            name="{{ $field['name'] }}"
            value="{{ $value ?? "" }}"
            @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
        >
        <a class="btn btn-default btn_upload_file btn-labeled {{ $hide }} {{ $errorClass }}" file_type='file'
            ratio="{{ $field['attributes']['ratio'] ?? $field['ratio'] ?? '' }}"
            @if(isset($field['file_type']))
                extension="{{ $field['file_type'] }}"
            @endif
            selecter="{{ $field['name'] }}"><span class="btn-label"><i class='fa fa-cloud-upload-alt'></i></span>Upload</a>
        {!! $img !!}
    </div>
    @if ($errors->has($field['name']))
        <div class="is-invalid"></div>
        <span class="invalid-feedback">{{ $errors->first($field['name']) }}</span>
    @endif
</div>