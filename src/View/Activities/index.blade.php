@extends(config('stlc.stlc_layout_path','stlc::layouts.app'))

@section('header')
    <section class="content-header">
        <h1>
            <span class="fa {{ $crud->icon }}"></span>
            <span class="text-capitalize">{{ $crud->labelPlural }}</span>
            <small>{{ trans('crud.all') }} <span>{{ $crud->labelPlural }}</span> {{ trans('crud.in_the_database') }}.</small>
        </h1>
        <ol class="breadcrumb">
            {{-- <li><a href="{{ url(config('backpack.base.route_prefix'), 'dashboard') }}">{{ trans('crud.admin') }}</a></li>
            <li><a href="{{ url($crud->route) }}" class="text-capitalize">{{ $crud->entity_name_plural }}</a></li>
            <li class="active">{{ trans('crud.list') }}</li> --}}
        </ol>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                {{-- <div class="box-header {{ $crud->hasAccess('create')?'with-border':'' }}">
                    @include('crud::inc.button_stack', ['stack' => 'top']) 
                    <div id="datatable_button_stack" class="pull-right text-right"></div>
                </div> --}}
                <div class="box-body">
                    {{-- propadmin List Filters --}}
                    {{-- @if ($crud->filtersEnabled())
                        @include('crud::inc.filters_navbar')
                    @endif --}}

                    <table id="crudTable" class="table table-bordered table-striped display">
                        <thead class="table-success">
                            <tr>
                                <th>Id</th>
                                @foreach ($crud->columns as $column)
                                <th>{{ $column['label'] }}</th>
                                @endforeach
                                <th>Details</th>
                            </tr>
                        </thead>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>
    </div>
@endsection

@push('after_styles')
    <style>
    td i.fa.fa-plus-circle.details-control {
        color:green !important;
        font-size: 150%;
        padding: 10px 12px;
    }
    tr.shown td i.fa.fa-minus-circle.details-control {
        color:red !important;
        font-size: 150%;
        padding: 10px 12px;
    }
    </style>
@endpush

@push('after_scripts')
    <script type="text/javascript">
        function format ( d ) {
            // `d` is the original data object for the row
            return '<pre>'+JSON.stringify(jQuery.parseJSON(d.data), null, 2)+"</pre>";
        }

        jQuery(document).ready(function($) {

            var table = $("#crudTable").DataTable({
                "pageLength": {{ $crud->getDefaultPageLength() }},
                /* Disable initial sort */
                "aaSorting": [],
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{!! url($crud->route.'/datatable') !!}",
                    "type": "POST"
                },
                dom: "<'row'<'col-sm-6'l><'col-sm-6'f>><'table-responsive clearfix'tr><'row'<'col-sm-5'i><'col-sm-7'p>>",
            });
            
            // Add event listener for opening and closing details
            $('#crudTable tbody').on('click', 'td i.fa.details-control', function () {
                var tr = $(this).closest('tr');
                var i = $(this).closest('td i');
                var row = table.row( tr );

                if ( row.child.isShown() ) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                    i.addClass('fa-plus-circle');
                    i.removeClass('fa-minus-circle');
                }
                else {
                    // Open this row
                    console.log();
                    $.ajax({
                        url: "{{ url($crud->route.'/get_data') }}/"+i.attr('id'),
                        type: "Post",
                        success: function ( data ) {
                            console.log(data);
                            
                            if(data.status == "success") {
                                row.child( format(data.activity) ).show();
                                tr.addClass('shown');
                                i.addClass('fa-minus-circle');
                                i.removeClass('fa-plus-circle');
                            } else {
                                
                            }
                        }
                    });
                }
            } );
        });
    </script>
@endpush
