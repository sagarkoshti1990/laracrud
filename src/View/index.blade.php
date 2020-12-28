@extends(config('stlc.stlc_layout_path','stlc::layouts.app'))
@section('header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <span class="{{ $crud->icon }}"></span>
                        <span class="text-capitalize">{{ $crud->labelPlural }}</span>
                        <small>{{ trans('stlc.list') }}</small>
                    </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url(config('stlc.route_prefix'), 'dashboard') }}">{{ trans('stlc.dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ url($crud->route) }}" class="text-capitalize">{{ $crud->labelPlural }}</a></li>
                        <li class="breadcrumb-item active">{{ trans('stlc.list') }}</li>
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
                </div>
            </div>
        </div>
    </div>
    <div id="add_modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-xl">
            <!-- Modal content-->
            <div class="modal-content">
                {!! Form::open(array('url' => $crud->route, 'method' => 'post', 'id' => 'add_form')) !!}
                    <div class="modal-header">
                        <h4 class="card-title">{{ trans('stlc.add') }} {{ $crud->label }}</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body pb5">
                        @if(isset($src))
                            {{ Form::hidden('src', $src) }}
                        @endif
                        @form($crud, [], ["class" => "col-md-6"],'input',true)
                    </div>
                    <div class="modal-footer">
                        @include(config('stlc.view_path.inc.form_save_buttons','stlc::inc.form_save_buttons'),['model_close' => 'add_modal'])
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
@push('after_scripts')
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#add_form').validate({
                submitHandler: function (form) {
                    $.ajax({
                        url:$(form).attr('action'),
                        method:$(form).attr('method'),
                        data:$(form).serialize()+'&src_ajax=1',
                        beforeSend: function(){
                            $(form).find('[type=submit]').attr('disabled', true).find('i').remove().append('<i class="fa fa-circle-notch fa-spin"></i>');
                        },
                        success: function(data) {
                            ajax_form_notification(form,data);
                        },
                        error:function(data) {
                            ajax_form_notification(form,data);
                        }
                    })
                    return false;
                }
            });
            var table = $("#crudTable").DataTable({
                "pageLength": {{ $crud->getDefaultPageLength() }},
                "aaSorting": [],
                "processing": true,
                "serverSide": true,
                "responsive": true,
                "language": {"paginate": {"next":">","previous":"<"}},
                "ajax": {
                    "url": "{!! url($crud->route.'/datatable') !!}",
                    "type": "POST",
                    "data": function(data){
                        data.filter = [['deleted_at', null]];
                        var deleted = "{{ $_GET['__deleted__'] ?? 'false' }}";
                        if(deleted == 'true') {
                            data.filter = [['deleted_at','!=',null]];
                        }
                        @foreach($filters ?? [] as $key => $value)
                            data.filter.push(['{{$key}}','{{$value}}']);
                        @endforeach
                    }
                },
                dom: "<'row'<'col-sm-8'i><'col-sm-4'f>><'mb-3'tr><'row'<'col-sm-3'l><'col-sm-9'p><'col-sm-1'>>",
            });
        });
    </script>
@endpush