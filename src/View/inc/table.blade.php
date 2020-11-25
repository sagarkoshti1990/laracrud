@if(isset($crud->name))
@if(isset($card) && $card == true)
<div class="row">
<div class="col-md-12">
<div class="card mob-card">
    @if(isset($add_btn) && $add_btn == true)
        @include('crud.inc.button_stack', ['stack' => 'top','crud' => $crud])
    @endif
<div class="card-body">
@else
<div class="parent-{{$crud->name}} {{$class ?? ""}}">
@endif
    <table class="table crudTable display table-{{$crud->name}} {{$table_class ?? ""}}" cellspacing="0" width="100%">
        <thead class="mob-hide">
            <tr>
                <th>ID</th>
                {{-- Table columns --}}
                @foreach ($crud->columns as $column)
                <th>{{ $column['label'] }}</th>
                @endforeach
                @if ( $crud->buttons->where('stack', 'line')->count() )
                    <th style="width: 110px;">{{ trans('stlc.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
@if(isset($card) && $card == true)
</div>
@else
</div></div></div></div>
@endif
@else
<h1>Table not found</h1>
@endif