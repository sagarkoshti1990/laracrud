@extends(config('stlc.stlc_layout_path','stlc::layouts.app'))

@section('header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>
                    <span class="fa {{ $crud->icon }}"></span>
                    <span class="text-capitalize">{{ $crud->labelPlural }}</span>
                    <small>{{trans('stlc.list')}}.</small>
                </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url(config('stlc.route_prefix'), 'dashboard') }}">{{ trans('stlc.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ url($crud->route) }}" class="text-capitalize">{{ $crud->labelPlural }}</a></li>
                    <li class="breadcrumb-item active">{{trans('stlc.list')}}</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mob-card">
                @include(config('stlc.view_path.inc.button_stack','stlc::inc.button_stack'), ['stack' => 'top'])
                <div class="card-body">
                    <table id="crudTable" class="{{ config('stlc.css.table','table display crudTable') }}">
                        <thead class="{{ config('stlc.css.thead','thead-light') }}">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Label</th>
                                <th>Table Name</th>
                                <th>Model</th>
                                <th>Controller</th>
                                <th style="min-width: 120px;">Attribute</th>
                                <th>icon</th>
                                @if ( $crud->buttons->where('stack', 'line')->count() )
                                    <th style="min-width: 90px;">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($modules as $module)
                                <tr>
                                    <td>{{ $module->id }}</td>
                                    <td><a href="{{ url($crud->route.'/'.$module->id) }}">{{ $module->name }}</a></td>
                                    <td>{{ $module->label }}</td>
                                    <td>{{ $module->table_name }}</td>
                                    <td>{{ $module->model }}</td>
                                    <td>{{ $module->controller }}</td>
                                    <td>{{ $module->represent_attr }}</td>
                                    <td><span class="fa {{ $module->icon }}"></span></td>
                                    @if ( $crud->buttons->where('stack', 'line')->count() )
                                        <td>
                                            @include(config('stlc.view_path.inc.button_stack','stlc::inc.button_stack'), ['stack' => 'line', 'crud' => $crud, 'item' => $module])
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div><!-- /.card-body -->
            </div><!-- /.card -->
        </div>
    </div>
@endsection

@push('after_styles')
    
@endpush

@push('after_scripts')
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            
            $('#add_form').validate();

            var table = $("#crudTable").DataTable({
                "pageLength": '100',
                "aaSorting": [],
                "processing": true,
                "responsive": true,
                "language": {"paginate": {"next":">","previous":"<"}},
                dom: "<'row'<'col-sm-8'i><'col-sm-4'f>><'mb-3'tr><'row'<'col-sm-3'l><'col-sm-9'p><'col-sm-1'>>",
            });
        });
    </script>
@endpush
