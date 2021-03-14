@php
    $ralations = $ralations ?? [];
    $link = $link ?? true;
@endphp
@if($link == true)
    @foreach ($ralations as $ralation)
        @foreach ($ralation->value as $value)
            @if($ralation->module->hasAccess('view'))
                <a @attributes($ralation->module,'show.nav_tabs',["class"=>"nav-item nav-link","data-toggle"=>"tab"])
                    href="#{{$ralation->module->name.'-'.$value['name']}}" data-target="#tab-{{$ralation->module->name.'-'.$value['name']}}"
                ><i class="{{ $ralation->module->icon }} mr-2"></i>{{$ralation->module->name.'-'.$value['label']}}</a>
            @endif
        @endforeach
    @endforeach
@else
    @foreach ($ralations as $ralation)
        @foreach ($ralation->value as $value)
            @if($ralation->module->hasAccess('view'))
                <div @attributes($ralation->module,'show.tab_pane',['class'=>"tab-pane fade"]) id="tab-{{$ralation->module->name.'-'.$value['name']}}">
                    <div class="row">
                        <div class="col-md-12">
                            <div @attributes($ralation->module,'index.card',['class'=>"card"])>
                                @if($ralation->module->hasAccess('create'))
                                    <button @attributes($ralation->module,'index.button.create',[
                                        'class'=>'btn btn-primary btn-lg position-fixed','type'=>"button",
                                        'style'=>"right:2rem;bottom:2rem;border-radius:100%;z-index: 1000;font-size:1.25rem !important;",
                                    ]) data-toggle="modal" data-target="#add_modal{{$ralation->module->name.'-'.$value['name']}}"><i class="{{ config('stlc.view.icon.button.create','fa fa-plus') }}"></i></button>
                                @endif
                                <div @attributes($ralation->module,'index.card_body',['class'=>"card-body"])>
                                    <table @attributes($ralation->module,'index.table',['class'=>'table display crudTable',"id"=>"crudTable".$ralation->module->name.'-'.$value['name']])>
                                        <thead @attributes($ralation->module,'index.thead',['class' => 'thead-light'])>
                                            <tr @attributes($ralation->module,'index.tr',[])>
                                                <th @attributes($ralation->module,'index.th_id',[])>ID</th>
                                                @foreach ($ralation->module->columns as $column)
                                                    <th @attributes($ralation->module,'index.th',[],$column['attributes'] ?? null)>{{ $column['label'] }}</th>
                                                @endforeach
                                                @if ( $ralation->module->buttons->where('stack', 'line')->count() )
                                                <th @attributes($ralation->module,'index.th_action',['style'=>'min-width:130px'])>{{ trans('stlc.actions') }}</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody @attributes($ralation->module,'index.tbody',[])>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($ralation->module->hasAccess('create'))
                        <div @attributes($ralation->module,'index.modal',['id'=>"add_modal".$ralation->module->name.'-'.$value['name'],'class'=>"modal fade",'role'=>"dialog"])>
                            <div @attributes($ralation->module,'index.modal_dialog',['class'=>"modal-dialog modal-xl"])>
                                <!-- Modal content-->
                                <div @attributes($ralation->module,'index.modal_content',['class'=>"modal-content"])>
                                    {!! Form::open($ralation->module->getViewAtrributes('index.submit_form',['url' => $ralation->module->route, 'method' => 'post', 'id' => 'add_form'.$ralation->module->name.'-'.$value['name']])) !!}
                                        <div @attributes($ralation->module,'index.modal_header',['class'=>"modal-header"])>
                                            <h4 @attributes($ralation->module,'index.card_title',['class'=>"card-title"])>{{ trans('stlc.add') }} {{ $ralation->module->label }}</h4>
                                            <button @attributes($ralation->module,'index.modal_button_close',['type'=>"button",'class'=>"close","data-dismiss"=>"modal"])>&times;</button>
                                        </div>
                                        <div @attributes($ralation->module,'index.modal_body',['class'=>"modal-body"])>
                                            {{ Form::hidden('src', $crud->route.'/'.$item->id) }}
                                            {{ Form::hidden($value['name'], $item->id) }}
                                            @form($ralation->module, ['remove' => [$value['name']]], ["class" => "col-md-6"],'input')
                                        </div>
                                        <div @attributes($ralation->module,'index.modal_footer',['class'=>"modal-footer"])>
                                            @include(config('stlc.view_path.inc.form_save_buttons','stlc::inc.form_save_buttons'),['model_close' => 'add_modal'.$ralation->module->name.'-'.$value['name']])
                                        </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                @push('after_scripts')
                    <script type="text/javascript">
                        jQuery(document).ready(function($) {
                            @if($ralation->module->hasAccess('create'))
                                $("#add_form{{$ralation->module->name.'-'.$value['name']}}").validate({
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
                            @endif

                            $("#crudTable{{$ralation->module->name.'-'.$value['name']}}").DataTable({
                                "pageLength": {{ $ralation->module->getDefaultPageLength() }},
                                "aaSorting": [],
                                "processing": true,
                                "serverSide": true,
                                "responsive": true,
                                "language": {"paginate": {"next":">","previous":"<"}},
                                "order": [[ 0, "desc" ]],
                                "ajax": {
                                    "url": "{!! url($ralation->module->route.'/datatable') !!}",
                                    "type": "POST",
                                    "data": function(data){
                                        data.filter = [['{{$value["name"]}}','{{$item->id}}'],['deleted_at', null]];
                                        // var deleted = "{{ $_GET['__deleted__'] ?? 'false' }}";
                                        // if(deleted == 'true') {
                                        //     data.filter = [['deleted_at','!=',null]];
                                        // }
                                        @foreach($filters ?? [] as $key => $value)
                                            data.filter.push(['{{$key}}','{{$value}}']);
                                        @endforeach
                                    },
                                    "dataSrc": function(d){
                                        if(d.data.length === 0 && d.status == '403'){
                                            var settings = $("#crudTable{{$ralation->module->name.'-'.$value['name']}}").DataTable().settings()[0];
                                            settings.oLanguage.sEmptyTable = d.message;
                                        }
                                        return d.data;
                                    }
                                },
                                dom: "<'row'<'col-sm-8'i><'col-sm-4'f>><'mb-3'tr><'row'<'col-sm-3'l><'col-sm-9'p><'col-sm-1'>>",
                            });
                            $("#crudTable{{$ralation->module->name.'-'.$value['name']}}").css("width","100%")
                        });
                    </script>
                @endpush
            @endif
        @endforeach
    @endforeach
@endif
