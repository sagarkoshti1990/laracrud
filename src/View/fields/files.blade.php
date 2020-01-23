<div @include('crud.inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <div>
            <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!} @if(isset($field['file_type'])) <span style="color:red"> (Only {{ $field['file_type'] }})</span> @endif</label>
        </div>
    @endif
    @php
        $img = "";
        if((isset($field['value']) && is_array(json_decode($field['value'])) && count(json_decode($field['value']))) || (isset($field['default']) && is_array(json_decode($field['default'])) && count(json_decode($field['default'])))) {
            if((isset($field['value'])) && is_array(json_decode($field['value'])) && count(json_decode($field['value']))) {
                $uploads = Sagartakle\Laracrud\Models\Upload::whereIn('id',json_decode($field['value']))->get();
            } else if((isset($field['default'])) && is_array(json_decode($field['default'])) && count(json_decode($field['default']))) {
                $uploads = Sagartakle\Laracrud\Models\Upload::whereIn('id',json_decode($field['default']))->get();
            }
            $img = "<div class='uploaded_files'>";
            foreach ($uploads as $key => $upload) {
                $url_file = url("files/" . $upload->hash . DIRECTORY_SEPARATOR . $upload->name);
                $img .= "<a class='uploaded_file2' upload_id='".$upload->id."' target='_blank' href='".$url_file."'>";

                $image = '';
                if(in_array($upload->extension, ["jpg", "jpeg", "png", "gif", "bmp"])) {
                    $url_file .= "?s=100";
                    $image = '<img src="'.$url_file.'">';
                } else if(in_array($upload->extension, ["ogg",'wav','mp3'])) {
                    $image = '<audio controls>
                        <source src="'.$url_file.'" type="audio/'.$upload->extension.'">
                        Your browser does not support the audio element.
                    </audio>';
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
            }
            $img .= "</div>";
        } else {
            $img = "<div class='uploaded_files'>";
            // $img .= "<a class='uploaded_file hide' target='_blank'>";
            // $img .= "<span id='img_icon'></span>";
            // $img .= "<i title='Remove File' class='fa fa-times'></i>";
            // $img .= "</a>";
            $img .= "</div>";
            $hide = "";
        }
    @endphp
    <div class="btn-group">
        <input
            type="hidden"
            name="{{ $field['name'] }}"
            value="{{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' )) }}"
            @include('crud.inc.field_attributes')
        >
        <a class="btn btn-default btn_upload_files btn-labeled" file_type='files'
            @if(isset($field['file_type']))
                extension="{{ $field['file_type'] }}"
            @endif
            selecter="{{ $field['name'] }}"><span class="btn-label"><i class='fa fa-cloud-upload'></i></span>Upload</a>
        <?php
            echo $img;
        ?>
    </div>
</div>
{{-- FIELD EXTRA CSS  --}}
{{-- push things in the after_styles section --}}
@if ($crud->checkIfOnce($field))
    @push('crud_fields_styles')
    @endpush
    @push('crud_fields_scripts')
        <script>
            jQuery(document).ready(function($){
                $.validator.setDefaults({ 
                    ignore: [],
                    // any other default options and/or rules
                });
            });
        </script>
    @endpush
@endif
{{-- Note: you can use  @if ($crud->checkIfOnce($field))  to only load some CSS/JS once, even though there are multiple instances of it --}}