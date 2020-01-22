<!-- CKeditor -->
<div @include('crud.inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
        @include('crud.inc.field_translatable_icon')
    @endif
    <textarea
    	id="ckeditor-{{ $field['name'] }}"
        name="{{ $field['name'] }}"
        @include('crud.inc.field_attributes')
    	>{{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' )) }}</textarea>

    @if ($errors->has($field['name']))
        <span class="help-block">{{ $errors->first($field['name']) }}</span>
    @endif
    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>
@php
    if(isset($field['attribute']) && is_array($field['attribute'])) {
        
        $arr = [
            'Superscript',
            'Subscript',
            'Source',
            'Save',
            'Templates',
            'NewPage',
            'Preview',
            'Print',
            'Cut',
            'Undo',
            'Copy',
            'Redo',
            'Paste',
            'PasteText',
            'PasteFromWord',
            'Find',
            'Replace',
            'SelectAll',
            'Scayt',
            'Form',
            'Checkbox',
            'Radio',
            'TextField',
            'Textarea',
            'Select',
            'Button',
            'ImageButton',
            'HiddenField',
            'NumberedList',
            'BulletedList',
            'Outdent',
            'Indent',
            'Blockquote',
            'CreateDiv',
            'JustifyLeft',
            'JustifyCenter',
            'JustifyRight',
            'JustifyBlock',
            'BidiLtr',
            'BidiRtl',
            'Language',
            'Link',
            'Unlink',
            'Anchor',
            'Image',
            'Flash',
            'Table',
            'HorizontalRule',
            'Smiley',
            'SpecialChar',
            'PageBreak',
            'Iframe',
            'FontSize',
            'Font',
            'Format',
            'Styles',
            'TextColor',
            'BGColor',
            'Maximize',
            'ShowBlocks',
            'About',
            'Bold',
            'Italic',
            'Underline',
            'Strike',
            'RemoveFormat',
            'CopyFormatting'
        ];
        $output = array_diff($arr, $field['attribute']);
    }
@endphp

@if ($crud->checkIfOnce($field))
    {{-- FIELD CSS - will be loaded in the after_styles section --}}
    @push('crud_fields_styles')
    @endpush
    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
        <script src="{{ asset('node_modules/admin-lte/bower_components/ckeditor/ckeditor.js') }}"></script>
        <script src="{{ asset('node_modules/admin-lte/bower_components/ckeditor/adapters/jquery.js') }}"></script>
    @endpush
@endif
@push('crud_fields_scripts')
<script>
    jQuery(document).ready(function($) {
        var arr = [];
        @if(isset($output) && is_array($output))
            arr = {removeButtons: "{{ implode(',',$output) }}"}
        @endif

        CKEDITOR.replace("{{ $field['name'] }}", arr);
        
        $.validator.setDefaults({ 
            ignore: ['.ignore']
        });
    });
</script>
@endpush

{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
