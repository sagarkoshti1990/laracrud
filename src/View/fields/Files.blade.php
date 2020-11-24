@php
    $field['attributes']['class'] = $field['attributes']['class']." d-none";
    $values = old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' ));
    $img = "<div class='uploaded_files'></div>";
    if(isset($values) && is_array($values) && count($values)) {
        $img = "<div class='uploaded_files'>";
        foreach ($values as $key => $value) {
            $img .= CustomHelper::showHtml($value,'uploaded_file2');
        }
        $img .= "</div>";
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
        <select name="{{ $field['name'] }}[]" @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes') multiple>
            @if (isset($values) && is_array($values) && count($values))
                @foreach ($values as $key => $value)
                    <option value="{{ $value }}" selected>{{ $value }}</option>
                @endforeach
            @endif
        </select>
        <a class="btn btn-default btn_upload_file btn-labeled {{ $errorClass }}" file_type='files'
            ratio="{{ $field['attributes']['ratio'] ?? $field['ratio'] ?? '' }}"
            image_public="{{ $field['attributes']['image_public'] ?? $field['image_public'] ?? '' }}"
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