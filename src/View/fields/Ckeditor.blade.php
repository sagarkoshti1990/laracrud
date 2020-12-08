<!-- Ckeditor -->
@php
    $ck_count= \FormBuilder::$count['Ckeditor'];
    $field['attributes']['id'] = $field['attributes']['id'].++$ck_count;
@endphp
<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']])>
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
    @endif
    <textarea name="{{ $field['name'] }}" @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')>
        {{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' )) }}
    </textarea>
    @if ($errors->has($field['name']))
        <div class="is-invalid"></div><span class="invalid-feedback">{{ $errors->first($field['name']) }}</span>
    @endif
    @if (isset($field['hint'])){{-- HINT --}}
        <p class="form-text">{!! $field['hint'] !!}</p>
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
    } else {
        // $output = [
        //     'Save','Templates','Print','NewPage','Cut','Undo','Copy','Redo','Paste','PasteText','PasteFromWord','Find','Replace','SelectAll','Scayt',
        //     'Form','Checkbox','Radio','TextField','Textarea','Select','Button','ImageButton','HiddenField','CopyFormatting','RemoveFormat',
        //     'Outdent','Indent','CreateDiv','BidiLtr','BidiRtl','Language','Link','Unlink','Anchor','Flash','SpecialChar','PageBreak','Iframe','About'
        // ];
    }
@endphp
@pushonce('crud_fields_styles')
@endpushonce
{{-- FIELD JS - will be loaded in the after_scripts section --}}
@pushonce('crud_fields_scripts')
    <script src="{{ asset('node_modules/ckeditor-full/ckeditor.js') }}"></script>
@endpushonce
@push('crud_fields_scripts')
<script>
    jQuery(document).ready(function($) {
        var arr = {};
        @if(isset($output) && is_array($output))
            arr.removeButtons = "{{ implode(',',$output) }}";
        @else
            arr.toolbar = [
                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript'] },
                { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
                { name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley','Preview'] },
                { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
                { name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source'] },
            ];
        @endif
        arr.height="100px";
        // arr.maxHeight="300px";
        arr.removePlugins= 'elementspath';
        arr.resize_enabled=false;
        arr.on = {
            instanceReady: function(e) {
                @if ($errors->has($field['name']))
                    e.editor.container.addClass('form-control');
                    e.editor.container.addClass('is-invalid');
                @endif
            },
            pluginsLoaded: function(event) {
                event.editor.dataProcessor.dataFilter.addRules({
                    elements: {
                        script: function(element) {
                            return false;
                        }
                    }
                });
            }
        }
        CKEDITOR.replace("{{ $field['attributes']['id'] }}", arr);
        CKEDITOR.instances["{{ $field['attributes']['id'] }}"].on('change',function(){
            $('[name="{{ $field['name'] }}"]').valid();
        });
    });
</script>
@endpush