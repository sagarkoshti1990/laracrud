<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if(isset($field['attributes']['profile_pic']) && $field['attributes']['profile_pic'])
        @php
            if(isset($field['value']) && is_numeric($field['value']) && $field['value']) {
                $url_img = str_replace('\\','/', CustomHelper::img($field['value']));
            } elseif(isset($field['default']) && is_numeric($field['default']) && $field['default']) {
                $url_img = str_replace('\\','/',CustomHelper::img($field['default']));
            } else {
                $url_img = asset('public/base/img/male_profile.jpg');
            }
        @endphp
        <!-- profile_pic btn -->
        <input
            type="hidden"
            name="{{ $field['name'] }}"
            value="{{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' )) }}"
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
            $img = "";
            if(isset($field['value']) && is_numeric($field['value']) && $field['value']) {
                $url_img = CustomHelper::img($field['value']).'?s=100';
                $img = "<div class='uploaded_image'>";
                $img .= "<img width='100' src='$url_img'>";
                $img .= "<i title='Remove Image' class='fa fa-times'></i>";
                $img .= "</div>";
                $hide = "hide";
            } elseif(isset($field['default']) && is_numeric($field['default']) && $field['default']) {
                $url_img = CustomHelper::img($field['default']).'?s=100';
                $img = "<div class='uploaded_image'>";
                $img .= "<img width='100' src='$url_img'>";
                $img .= "<i title='Remove Image' class='fa fa-times'></i>";
                $img .= "</div>";
                $hide = "hide";
            } else {
                $img = "<div class='uploaded_image hide'>";
                $img .= "<img src=''>";
                $img .= "<i title='Remove Image' class='fa fa-times'></i>";
                $img .= "</div>";
                $hide = "";
            }
        @endphp
        <div class="btn-group">
            <input
                type="hidden"
                name="{{ $field['name'] }}"
                value="{{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' )) }}"
                @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
            >
            <a class="btn btn-default btn_upload_image btn-labeled {{ $hide }}" file_type='image' selecter="{{ $field['name'] }}" ratio={{ $field['attributes']['ratio'] ?? $field['ratio'] ?? '' }}>
                <span class="btn-label"><i class='fa fa-cloud-upload'></i></span>Upload</a>
            <?php
                echo $img;
            ?>
        </div>
    @endif
</div>