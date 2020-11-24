
@php
    $data = json_decode($item->data,true);
    if(isset($data['old']) && count($data['old']) && $data['new'] && count($data['new']))
    {
        $count = (count($data['old']) > count($data['new'])) ? array_keys($data['old']) : array_keys($data['new']);
    } else {
        $count = [];
    }
    $context_crud = config('stlc.module_model')::make((new $item->context_type)->get_module()->name);
@endphp
<h4>{{ $item->description.". Following are the change parameter" }}</h4><br>
<table class="table table-bordered table-condensed" style="background: #fff;margin: 10px;clear: both;width: -webkit-fill-available;">
    <tr>
        <th>Name</th>
        <th>New</th>
        <th>Old</th>
    </tr>
    @for($i=0; $i<count($count); $i++)
        <tr>
            <td>{{ $count[$i] }}</td>
            @php
                $context_crud->row = $data['new'];
            @endphp
            <td>{!! (isset($data['new'][$count[$i]]) && $count[$i] == 'updated_at') ? \CustomHelper::date_format($data['new'][$count[$i]], 'field_show_with_time') : isset($data['new'][$count[$i]]) ? \FormBuilder::get_field_value($context_crud, $count[$i]) : '' !!}</td>
            @php
                $context_crud->row = $data['old'];
            @endphp
            <td>{!! (isset($data['old'][$count[$i]]) && $count[$i] == 'updated_at') ? \CustomHelper::date_format($data['old'][$count[$i]], 'field_show_with_time') : isset($data['old'][$count[$i]]) ? \FormBuilder::get_field_value($context_crud, $count[$i]) : '' !!}</td>
        </tr>
    @endfor
</table>