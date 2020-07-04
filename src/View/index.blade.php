@extends('layouts.app')
@section('header')
    <section class="content-header">
        <h1>
            <span class="fa {{ $crud->icon }}"></span>
            <span class="text-capitalize">{{ $crud->labelPlural }}</span>
            <small>List.</small>
        </h1>
        <ol class="breadcrumb float-right">
            <li class="breadcrumb-item"><a href="{{ url(config('stlc.route_prefix'), 'dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url($crud->route) }}" class="text-capitalize">{{ $crud->labelPlural }}</a></li>
            <li class="breadcrumb-item active">List</li>
        </ol>
    </section>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box mob-box">
                @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.button_stack', ['stack' => 'top'])
                <div class="box-body">
                    <table id="crudTable" class="table crudTable display" cellspacing="0" width="100%">
                        <thead class="table-success mob-hide">
                            <tr>
                                <th>{{ $crud->label }} ID</th>
                                {{-- Table columns --}}
                                @foreach ($crud->columns as $column)
                                <th>{{ $column['label'] }}</th>
                                @endforeach
                                @if ( $crud->buttons->where('stack', 'line')->count() )
                                <th style="width: 110px;">Actions</th>
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
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                {!! Form::open(array('url' => $crud->route, 'method' => 'post', 'id' => 'add_form')) !!}
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="box-title">Add {{ $crud->label }}</h4>
                    </div>
                    <div class="modal-body pb5">
                        @if(isset($src))
                            {{ Form::hidden('src', $src) }}
                        @endif
                        @form($crud, [], ["class" => "col-md-6"])
                    </div>
                    <div class="modal-footer">
                        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.form_save_buttons',['model_close' => 'add_modal'])
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
                            $(form).find('[type=submit]').attr('disabled', true).find('i').remove().append('<i class="fa fa-circle-o-notch fa-spin"></i>');
                        },
                        success: function(data) {
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
                },
                dom: "<'row'<'col-sm-8'i><'col-sm-4'f>><'mb-3'tr><'row'<'col-sm-3'l><'col-sm-9'p><'col-sm-1'>>",
            });
        });
    </script>
@endpush
