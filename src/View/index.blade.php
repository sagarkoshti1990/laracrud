@extends(config('stlc.stlc_layout_path','stlc::layouts.app'))
@section('header')
    @include(config('stlc.view_path.inc.header','stlc::inc.header'),['sub_headeing'=>trans('stlc.list')])
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div @attributes($crud,'index.card',['class'=>"card"])>
                @include(config('stlc.view_path.inc.button_stack','stlc::inc.button_stack'), ['stack' => 'top'])
                <div @attributes($crud,'index.card_body',['class'=>"card-body"])>
                    <table @attributes($crud,'index.table',['class'=>'table display crudTable',"id"=>"crudTable"])>
                        <thead @attributes($crud,'index.thead',['class' => 'thead-light'])>
                            <tr @attributes($crud,'index.tr',[])>
                                <th @attributes($crud,'index.th_id',[])>ID</th>
                                @foreach ($crud->columns as $column)
                                    <th @attributes($crud,'index.th',[],$column['attributes'] ?? null)>{{ $column['label'] }}</th>
                                @endforeach
                                @if ( $crud->buttons->where('stack', 'line')->count() )
                                <th @attributes($crud,'index.th_action',['style'=>'min-width:130px'])>{{ trans('stlc.actions') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody @attributes($crud,'index.tbody',[])>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div @attributes($crud,'index.modal',['id'=>"add_modal",'class'=>"modal fade",'role'=>"dialog"])>
        <div @attributes($crud,'index.modal_dialog',['class'=>"modal-dialog modal-xl"])>
            <!-- Modal content-->
            <div @attributes($crud,'index.modal_content',['class'=>"modal-content"])>
                {!! Form::open($crud->getViewAtrributes('index.submit_form',['url' => $crud->route, 'method' => 'post', 'id' => 'add_form'])) !!}
                    <div @attributes($crud,'index.modal_header',['class'=>"modal-header"])>
                        <h4 @attributes($crud,'index.card_title',['class'=>"card-title"])>{{ trans('stlc.add') }} {{ $crud->label }}</h4>
                        <button @attributes($crud,'index.modal_button_close',['type'=>"button",'class'=>"close","data-dismiss"=>"modal"])>&times;</button>
                    </div>
                    <div @attributes($crud,'index.modal_body',['class'=>"modal-body"])>
                        @if(isset($src))
                            {{ Form::hidden('src', $src) }}
                        @endif
                        @form($crud, [], ["class" => "col-md-6"],'input',true)
                    </div>
                    <div @attributes($crud,'index.modal_footer',['class'=>"modal-footer"])>
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
                "order": [[ 0, "desc" ]],
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