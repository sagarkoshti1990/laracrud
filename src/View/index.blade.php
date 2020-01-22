@extends('layouts.app')
@section('header')
    <section class="content-header">
        <div class="row">
            <div class="col-md-6">
                <h1>
                    <span class="fa {{ $crud->icon }}"></span>
                    <span class="text-capitalize">{{ $crud->labelPlural }}</span>
                    <small>{{ trans('crud.all') }} <span>{{ $crud->labelPlural }}</span> {{ trans('crud.in_the_database') }}.</small>
                </h1>
            </div>
            <div class="col-md-6">
            </div>
        </div>
    </section>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box mob-box">
                @include('crud.inc.button_stack', ['stack' => 'top'])
                <div class="box-body">
                    <table id="crudTable" class="table crudTable display" cellspacing="0" width="100%">
                        <thead class="table-success mob-hide">
                            <tr>
                                <th>id</th>
                                {{-- Table columns --}}
                                @foreach ($crud->columns as $column)
                                <th>{{ $column['label'] }}</th>
                                @endforeach
                                @if ( $crud->buttons->where('stack', 'line')->count() )
                                <th style="width: 110px;">{{ trans('crud.actions') }}</th>
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
                        <h4 class="box-title">{{ trans('crud.add_a_new') }} {{ $crud->label }}</h4>
                    </div>
                    <div class="modal-body pb5">
                        @if(isset($src))
                            {{ Form::hidden('src', $src) }}
                        @endif
                        @form($crud, [], ["class" => "col-md-6"])
                    </div>
                    <div class="modal-footer">
                        @include('crud.inc.form_save_buttons',['model_close' => 'add_modal'])
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
                dom: "<'row'<'col-sm-8'i><'col-sm-4'f>><'mb20'tr><'row'<'col-sm-3'l><'col-sm-9'p><'col-sm-1'>>",
            });
        });
    </script>
@endpush
