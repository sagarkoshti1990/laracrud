<!-- select2 from ajax -->
@php
    $connected_entity = new $field['model'];
    $connected_entity_key_name = $connected_entity->getKeyName();
    $old_value = old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : false ));
@endphp
@component(config('stlc.view_path.inc.input_group','stlc::inc.input_group'),['field' => $field])
    @slot('onInput')
        <?php $entity_model = $crud->model; ?>
        <select
            name="{{ $field['name'] }}"
            style="width: 100%"
            id="select2_ajax_{{ $field['name'] }}"
            @include(config('stlc.view_path.inc.field_attributes','stlc::inc.field_attributes'), ['default_class' =>  'form-control'])
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
    @endslot
@endcomponent
@pushonce('crud_fields_styles')
    <link href="{{ asset('node_modules/admin-lte/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('node_modules/admin-lte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .select2.select2-container{flex: 1 1 0%;}
    </style>
@endpushonce
@pushonce('crud_fields_scripts')
    <script src="{{ asset('node_modules/admin-lte/plugins/select2/js/select2.full.min.js') }}"></script>
@endpushonce
@push('crud_fields_scripts')
<script>
    jQuery(document).ready(function($) {
        // trigger select2 for each untriggered select2 box
        $("#select2_ajax_{{ $field['name'] }}").each(function (i, obj) {
            if (!$(obj).hasClass("select2-hidden-accessible")) {
                $(obj).select2({
                    theme:"bootstrap4",
                    placeholder: $(this).attr('placeholder'),
                    minimumInputLength: "{{ $field['minimum_input_length'] ?? '0' }}",
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
                            console.log(data, params);
                            params.page = params.page || 1;
                            var result = {
                                results: $.map(data.item, function (item) {
                                    return {text: item['text'],id: item["value"]}
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