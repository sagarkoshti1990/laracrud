@extends(config('stlc.stlc_layout_path','stlc::layouts.app'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-widget widget-user mb-0 bg-info">
                <div class="bg-dark-purple p-3">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="widget-user-username">{{ $module->$represent_attr }}</h3>
                            {{--  <h5 class="widget-user-desc">Founder &amp; CEO</h5>  --}}
                        </div>
                        <div class="col-md-6 text-right">
                            <button data-toggle="modal" data-target="#add_field_modal" class="btn btn-flat bg-green btn-sm btn-with-icon">
                                <span class="ladda-label"><i class="fa fa-plus"></i> Field</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="widget-user-image" style="top: 5px;">
                    <span class="info-box-icon" style="border-radius:100%;height: 55px;width: 55px;font-size: 30px;line-height: 60px;"><i class="{{ $crud->module->icon }}"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link" href="{{ $src ?? url($crud->route) }}"><i class="fa fa-arrow-left"></i></a></li>
                    <a class="nav-item nav-link" href="#information"  data-target="#tab-information" data-toggle="tab"><i class="fa fa-info mr-2"></i>Information</a></li>
                    <a class="nav-item nav-link active" href="#Fields" data-target="#tab-Fields" data-toggle="tab"><i class="fa fa-list mr-2"></i>Fields</a></li>
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade in" id="tab-information">
                        <div class="tab-content">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h4 class="box-title">Information</h4>
                                </div>
                                <div class="box-body">
                                    @displayAll($crud, [], ["class" => "col-md-6"])
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade in active show" id="tab-Fields">
                        <div class="tab-content">
                            <div class="box infolist">
                                <div class="box-body table-responsive">
                                    <table id="crudTable" class="table table-bordered table-striped display crudTable">
                                        <thead class="table-success">
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Label</th>
                                                <th style="min-width: 60px;">F-Type</th>
                                                <th>Rank</th>
                                                <th>Unique</th>
                                                <th style="min-width: 80px;">Default-V</th>
                                                <th style="min-width: 50px;">Min-L</th>
                                                <th style="min-width: 50px;">Max-L</th>
                                                <th>Required</th>
                                                <th style="min-width: 60px;">Show</th>
                                                <th style="min-width: 60px;">Json-v</th>
                                                @if ( $crud->buttons->where('stack', 'line')->count() )
                                                    <th style="min-width: 100px;">Action</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($module->fields as $field)
                                                <tr>
                                                    <td>{{ $field->id }}</td>
                                                    <td>
                                                        <a href="{{ url($crud_filed->route.'/'.$field->id.'?src='.$crud->route.'/'.$module->id) }}">
                                                            {{ $field->name }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $field->label ?? "" }}</td>
                                                    <td>{{ $field->field_type->name ?? "" }}</td>
                                                    <td>{{ $field->rank ?? "" }}</td>
                                                    <td>{{ $field->unique ?? "" }}</td>
                                                    <td>{{ $field->defaultvalue ?? "" }}</td>
                                                    <td>{{ $field->minlength ?? "" }}</td>
                                                    <td>{{ $field->maxlength ?? "" }}</td>
                                                    <td>{{ $field->required ?? "" }}</td>
                                                    <td>{{ $field->show_index ?? "" }}</td>
                                                    <td>{{ $field->json_values ?? "" }}</td>
                                                    @if ( $crud->buttons->where('stack', 'line')->count() )
                                                        <td>
                                                            @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.button_stack', ['stack' => 'line', 'src' => $crud->route.'/'.$module->id, 'crud' => $crud_filed, 'entry' => $field])
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="add_field_modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Modal Heading</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                {!! Form::open(array('url' => $crud_filed->route, 'method' => 'Pot', 'id' => 'add_form', "autocomplete"=>"off")) !!}
                    <input type="hidden" name="module_id" value="{{$module->id}}">
                    {{ Form::hidden('src', $crud->route.'/'.$module->id) }}
                    <div class="modal-body">
                        @form($crud_filed,[
                            'name','label','rank','field_type_id','unique','defaultvalue','minlength',
                            'maxlength','required','show_index','json_type'
                        ],['class' => 'col-md-6'])
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="json_values" class="control-label">Json Values</label>
                                    <select id="json_values" class="form-control select2_field"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border">
                        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.form_save_buttons', ['model_close' => "add_field_modal"])
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('after_styles')
<style>
    section.content{
        padding: 0;
    }
    .select2-container{ width: 100% !important; }
</style>
@endpush

@push('after_scripts')
<script type="text/javascript">
jQuery(document).ready(function($) {

    $('#add_form').validate();

    select_data($('#add_form :input[name="json_type"]'));

    $('#add_form :input[name="json_type"]').on('ifChecked',function(e){
        select_data(this);
    });

    var table = $("#crudTable").DataTable({
        "pageLength": '100',
        "aaSorting": [],
        "processing": true,
        "responsive": true,
        "language": {"paginate": {"next":">","previous":"<"}},
        dom: "<'row'<'col-sm-8'i><'col-sm-4'f>><'mb-3'tr><'row'<'col-sm-3'l><'col-sm-9'p><'col-sm-1'>>",
    });

    function select_data($element) {
        $(":input#json_values").html();
        if($element.value == "Json") {
            $(":input#json_values").attr('multiple',true);
            $(":input#json_values").attr('name',"json_values[]");
            $(":input#json_values").select2({
                placeholder: "Enter Json",
                tags: true,
            })
        } else {
            $(":input#json_values").attr('multiple',false);
            $(":input#json_values").attr('name',"json_values");
            $(":input#json_values").select2({
                placeholder: "Select Module",
                ajax: {
                    url: "{{ url(config('lara.base.route_prefix').'/data_select') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            searchTerm: params.term // search term
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
        }
    }
});
</script>
@endpush