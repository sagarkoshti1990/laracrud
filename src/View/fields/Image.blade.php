@php
    $value = old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' ));
	$errorClass = $errors->has($field['name']) ? 'form-control is-invalid' : "";
@endphp
<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if(isset($field['attributes']['profile_pic']) && $field['attributes']['profile_pic'])
        @php
            if(isset($value) && is_numeric($value) && $value) {
                $url_img = str_replace('\\','/', CustomHelper::img($value));
            } else {
                $url_img = asset('public/base/img/male_profile.jpg');
            }
        @endphp
        <!-- profile_pic btn -->
        <input
            type="hidden"
            name="{{ $field['name'] }}"
            value="{{ $value ?? "" }}"
            @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
        >
        <a class="profile-pic" file_type='image' selecter="{{ $field['name'] }}" ratio={{ $field['attributes']['ratio'] ?? $field['ratio'] ?? '' }}>
            <div class="profile-pic profile-pic-img" style="background-image: url('{{ $url_img }}')" >
                <span class="fa fa-upload"></span>
            </div>
        </a>
    @else
        @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
            <div>
                <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
            </div>
        @endif
        @php
            $img = $hide = "";
            if(isset($value) && is_numeric($value) && $value) {
                $img = CustomHelper::showHtml($value);
                $hide = "d-none";
            }
        @endphp
        <div class="btn-group">
            <input
                type="hidden"
                name="{{ $field['name'] }}"
                value="{{ $value ?? "" }}"
                @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
            >
            <a class="btn btn-default btn_upload_file btn-labeled {{ $hide }} {{ $errorClass }}" file_type='image' selecter="{{ $field['name'] }}" ratio={{ $field['attributes']['ratio'] ?? $field['ratio'] ?? '' }}>
                <span class="btn-label"><i class='fa fa-cloud-upload-alt'></i></span>Upload</a>
            <?php
                echo $img;
            ?>
        </div>
    @endif
    @if ($errors->has($field['name']))
        <div class="is-invalid"></div>
        <span class="invalid-feedback">{{ $errors->first($field['name']) }}</span>
    @endif
</div>