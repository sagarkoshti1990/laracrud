@php
    $item_name = strtolower(isset($field['entity_singular']) && !empty($field['entity_singular']) ? $field['entity_singular'] : $field['label']);
    $items = old($field['name']) ? (old($field['name'])) : (isset($field['value']) ? ($field['value']) : (isset($field['default']) ? ($field['default']) : '' ));
    if (is_string($items) && !is_array(json_decode($items))) {
        $items = [];
    } else if(is_string($items)){
        $items = json_decode($items);
    }
    $rowHtml = "<tr class='array-row'>";
    foreach( $field['columns'] as $prop => $label) {
        $rowHtml .= "<td>
            <input class='form-control form-control-sm' type='text' name='".$field['name']."[0][".$label."]'>
        </td>";
    }
    $rowHtml .= "<td>
        <button class='row-array-controls btn btn-danger btn-sm' type='button'><i class='fa fa-times'></i></button></td>
        </tr>";
@endphp
@component(config('stlc.view_path.inc.input_group','stlc::inc.input_group'),['field' => $field])
    @slot('onInput')
        <div class="array-container" style="flex: 1 1 0%;">
            <table class="table table-striped m-b-0">
                <thead>
                    <tr>
                        @foreach( $field['columns'] as $prop )
                            <th style="font-weight: 600!important;">{{ $prop }}</th>
                        @endforeach
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($items) && is_array($items) && count($items) > 0)
                        @foreach ($items as $key => $item)
                            <tr class='array-row'>
                                @foreach( $field['columns'] as $prop => $label)
                                    <td>
                                        <input
                                            class='form-control form-control-sm' type='text'
                                            name='{{ $field['name']."[".$key."][".$label."]" }}'
                                            value="{{ (is_object($item) && isset($item->{$label})) ? $item->{$label} : ( (is_array($item) && isset($item[$label])) ? $item[$label] : "") }}"
                                        >
                                    </td>
                                @endforeach
                                <td>
                                    <button class='row-array-controls btn btn-danger btn-sm' type='button'><i class='fa fa-times'></i></button>
                                </td>
                            </tr>
                        @endforeach
                    @else {!! $rowHtml !!} @endif
                </tbody>
            </table>
            <div class="array-controls btn-group m-t-10">
                <button class="btn btn-sm btn-default" type="button"><i class="fa fa-plus"></i> Add {!! $item_name !!}</button>
            </div>
        </div>
    @endslot
@endcomponent
{{-- FIELD CSS - will be loaded in the after_styles section --}}
@pushonce('crud_fields_styles')
    <style>
        tr.array-row>td:last-child{padding:.75rem 0rem;}
    </style>
@endpushonce
@pushonce('crud_fields_scripts')
    <script>
        $(function (param) {
            $('.array-container table').on('click','tr.array-row button',function(){
                if($(this).closest('table').find('tr.array-row').length > 1) {
                    $(this).closest('tr').remove();
                } else {
                    $(this).closest('tr').find(':input').val(null);
                }
            });
            $('.array-controls button').on('click',function(){
                var rowhtml = `{!! $rowHtml ?? '' !!}`;
                var count = 0;
                if(isset($('tr.array-row'))) {
                    count = $('tr.array-row').length;
                }
                if(count >= 0) {
                    rowhtml = rowhtml.replaceAll('[0]',`[${count}]`);
                    $('.array-container table tbody').append(rowhtml);
                }
            });
        });
    </script>
@endpushonce