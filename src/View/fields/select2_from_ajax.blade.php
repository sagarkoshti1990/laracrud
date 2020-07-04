<!-- select2 from ajax -->
@php
    $connected_entity = new $field['model'];
    $connected_entity_key_name = $connected_entity->getKeyName();
    $old_value = old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : false ));
@endphp

<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
    @endif
    <?php $entity_model = $crud->model; ?>

    <select
        name="{{ $field['name'] }}"
        style="width: 100%"
        {{-- id="select2_ajax_{{ $field['name'] }}" --}}
        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes', ['default_class' =>  'form-control'])
        >

        @if ($old_value)
            @php
                $item = $connected_entity->find($old_value);
            @endphp
            @if ($item)
            <option value="{{ $item->getKey() }}" selected>
                {{ $item->{$field['attribute']} }}
            </option>
            @endif
        @endif
    </select>
    @if ($errors->has($field['name']))
        <span class="help-block">{{ $errors->first($field['name']) }}</span>
    @endif
    @if (isset($field['hint'])){{-- HINT --}}
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>
{{-- FIELD CSS - will be loaded in the after_styles section --}}
@pushonce('crud_fields_styles')
<!-- include select2 css-->
<link href="{{ asset('node_modules/admin-lte/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
{{--  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />  --}}
{{-- <link href="{{ asset('public/vendor/select2/select2-bootstrap.css') }}" rel="stylesheet" type="text/css" /> --}}
@endpushonce
{{-- FIELD JS - will be loaded in the after_scripts section --}}
@pushonce('crud_fields_scripts')
<!-- include select2 js-->
<script src="{{ asset('node_modules/admin-lte/bower_components/select2/dist/js/select2.min.js') }}"></script>
@endpushonce
@push('crud_fields_scripts')
<script>
    jQuery(document).ready(function($) {
        // trigger select2 for each untriggered select2 box
        $("#select2_ajax_{{ $field['name'] }}").each(function (i, obj) {
            if (!$(obj).hasClass("select2-hidden-accessible"))
            {
                $(obj).select2({
                    theme: 'bootstrap',
                    multiple: false,
                    placeholder: "{{ $field['attributes']['placeholder'] }}",
                    minimumInputLength: "{{ $field['minimum_input_length'] ?? '3' }}",
                    ajax: {
                        url: "{{ $field['data_source'] }}?get_data_ajax=1",
                        dataType: 'json',
                        quietMillis: 250,
                        data: function (params) {
                            return {
                                q: params.term, // search term
                                page: params.page
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;

                            var result = {
                                results: $.map(data.item.data, function (item) {
                                    textField = "{{ $field['attribute'] }}";
                                    return {
                                        text: item[textField],
                                        id: item["{{ $connected_entity_key_name }}"]
                                    }
                                }),
                                more: data.current_page < data.last_page
                            };
                            return result;
                        },
                        cache: true
                    },
                });
            }
        });
    });
</script>
@endpush