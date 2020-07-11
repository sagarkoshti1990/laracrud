<!-- CKeditor -->
@php
    $ck_count= \FormBuilder::$count['CKEditor'];
    $field['attributes']['id'] = $field['attributes']['id'].++$ck_count;
@endphp
<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']])>
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_translatable_icon')
    @endif
    <textarea name="{{ $field['name'] }}" @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')>
        {{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' )) }}
    </textarea>
    @if ($errors->has($field['name']))
        <span class="help-block">
            <strong>{{ $errors->first($field['name']) }}</strong>
        </span>
    @endif
    @if (isset($field['hint'])){{-- HINT --}}
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>
@php
    if(isset($field['attribute']) && is_array($field['attribute'])) {
        $arr = [
            'Superscript','Subscript','Source','Save','Templates','NewPage','Preview','Print',
            'Cut','Undo','Copy','Redo','Paste','PasteText','PasteFromWord','Find','Replace',
            'SelectAll','Scayt','Form','Checkbox','Radio','TextField','Textarea','Select',
            'Button','ImageButton','HiddenField','NumberedList','BulletedList','Outdent','Indent',
            'Blockquote','CreateDiv','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock',
            'BidiLtr','BidiRtl','Language','Link','Unlink','Anchor','Image','Flash','Table',
            'HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe','FontSize','Font','Format',
            'Styles','TextColor','BGColor','Maximize','ShowBlocks','About','Bold','Italic','Underline',
            'Strike','RemoveFormat','CopyFormatting'
        ];
        $output = array_diff($arr, $field['attribute']);
    }
    // $output = ['Bold'];
@endphp
@pushonce('crud_fields_styles')
@endpushonce
{{-- FIELD JS - will be loaded in the after_scripts section --}}
@pushonce('crud_fields_scripts')
    <script src="{{ asset('node_modules/admin-lte/bower_components/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('node_modules/admin-lte/bower_components/ckeditor/adapters/jquery.js') }}"></script>
@endpushonce
@push('crud_fields_scripts')
<script>
    jQuery(document).ready(function($) {
        var arr = {};
        @if(isset($output) && is_array($output))
            arr.removeButtons = "{{ implode(',',$output) }}";
        @endif
        arr.toolbar = [
            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
            { name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
            { name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
            { name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
            { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
            { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
            { name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
            { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
            { name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
        ];
        arr.height="70px";
        arr.extraPlugins='autogrow';
        arr.autoGrow_minHeight="{{ $field['attributes']['minHeight'] ?? '70' }}";
        arr.autoGrow_maxHeight="{{ $field['attributes']['maxHeight'] ?? '300' }}";
        arr.removePlugins= 'elementspath';
        arr.resize_enabled=false;
        arr.plugins = 'mentions,emoji,basicstyles,undo,link,wysiwygarea,toolbar',
        arr.toolbar = [{
                name: 'document',
                items: ['Undo', 'Redo']
            },
            {
                name: 'basicstyles',
                items: ['Bold', 'Italic', 'Strike']
            },
            {
                name: 'links',
                items: ['EmojiPanel', 'Link', 'Unlink']
            }
        ];
        CKEDITOR.replace("{{ $field['attributes']['id'] }}", arr);
    });
</script>
@endpush