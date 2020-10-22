<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <div>
            <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!} @if(isset($field['file_type'])) <span style="color:red"> (Only {{ $field['file_type'] }})</span> @endif</label>
        </div>
    @endif
    @php
        $img = $hide = "";
        if((isset($field['value']) && is_numeric($field['value']) && $field['value']) || (isset($field['default']) && is_numeric($field['default']) && $field['default'])) {
            $upload = App\Models\Upload::find($field['value']);
            if(isset($upload->id)) {
                $url_file = url("files/" . $upload->hash . DIRECTORY_SEPARATOR . $upload->name);
                $img = "<a class='uploaded_file' target='_blank' href='".$url_file."'>";

                $image = '';
                if(in_array($upload->extension, ["jpg", "JPG", "jpeg", "png", "gif", "bmp"])) {
                    $url_file .= "?s=100";
                    $image = '<img  width="100" src="'.$url_file.'">';
                } else if(in_array($upload->extension, ["ogg",'wav','mp3'])) {
                    $image = '<audio controls>
                        <source src="'.$url_file.'" type="audio/'.$upload->extension.'">
                        Your browser does not support the audio element.
                    </audio>';
                } else if(in_array($upload->extension, ["mp4","WEBM","MPEG","AVI","WMV","MOV","FLV","SWF"])) {
                    $image = '<i class="fa fa-file-video-o"></i>';
                } else {
                    switch ($upload->extension) {
                        case "pdf":
                        $image = '<i class="fa fa-file-pdf-o"></i>';
                        break;
                    case "xls":
                        $image = '<i class="fa fa-file-excel-o"></i>';
                        break;
                    case "docx":
                        $image = '<i class="fa fa-file-word-o"></i>';
                        break;
                    case "xlsx":
                        $image = '<i class="fa fa-file-excel-o"></i>';
                        break;
                    case "csv":
                        $image += '<span class="fa-stack" style="color: #31A867 !important;">';
                        $image += '<i class="fa fa-file-o fa-stack-2x"></i>';
                        $image += '<strong class="fa-stack-1x">CSV</strong>';
                        $image += '</span>';
                        break;
                    default:
                        $image = '<i class="fa fa-file-text-o"></i>';
                        break;
                    }
                }
                
                $img .= "<span id='img_icon'>$image</span>";
                $img .= "<i title='Remove File' class='fa fa-times'></i>";
                $img .= "</a>";
                $hide = "hide";
            }
        } else {
            $img = "<a class='uploaded_file hide' target='_blank'>";
            $img .= "<span id='img_icon'></span>";
            $img .= "<i title='Remove File' class='fa fa-times'></i>";
            $img .= "</a>";
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
        <a class="btn btn-default btn_upload_file btn-labeled {{ $hide }}" file_type='file'
            ratio="{{ $field['attributes']['ratio'] ?? $field['ratio'] ?? '' }}"
            @if(isset($field['file_type']))
                extension="{{ $field['file_type'] }}"
            @endif
            selecter="{{ $field['name'] }}"><span class="btn-label"><i class='fa fa-cloud-upload-alt'></i></span>Upload</a>
        <?php
            echo $img;
        ?>
    </div>
</div>