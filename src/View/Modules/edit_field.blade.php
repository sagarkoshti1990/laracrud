@extends(config('stlc.stlc_layout_path','stlc::layouts.app'))
@section('header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>
                    <a href="{{ url($crud->route) }}">
                        <span class="{{ $crud->icon }}"></span>
                        <span class="text-capitalize">{{ $crud->label }}</span>
                    </a>
                    <small>{{trans('stlc.edit')}}</small>
                </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url(config('stlc.route_prefix'), 'dashboard') }}">{{ trans('stlc.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ url($crud->route) }}" class="text-capitalize">{{ $crud->labelPlural }}</a></li>
                    <li class="breadcrumb-item active">{{trans('stlc.edit')}}</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        {!! Form::open(array('url' => $crud->route.'/'.$field->id, 'method' => 'put', 'id' => 'edit_form', "autocomplete"=>"off")) !!}
            <div class="card">
                <div class="card-body">
                    @if(isset($src))
                        {{ Form::hidden('src', $src) }}
                    @endif
                    @form($crud,[
                        'name','label','rank','field_type_id','unique','defaultvalue','minlength',
                        'maxlength','required','show_index','json_type'
                    ],['class' => 'col-md-6'])
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="json_values" class="control-label">Json Values</label>
                                <select id="json_values" class="form-control"></select>
                            </div>
                        </div>
                    </div>
                </div><!-- /.card-body -->
                <div class="card-footer">
                    @include(config('stlc.view_path.inc.form_save_buttons','stlc::inc.form_save_buttons'))
                </div>
            </div><!-- /.card -->
        {!! Form::close() !!}
    </div>
</div>
@endsection
@push('after_styles')
<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #3c8dbc;border-color: #367fa9;padding: 1px 10px;
    }
    .select2-container--default .select2-selection--multiple{border-radius: 0;}
</style>
@endpush
@push('after_scripts')
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#edit_form').validate();
        
        @if(is_string($field->json_values) && \Str::startsWith($field->json_values, "@"))
            $('#edit_form :input[name="json_type"][value=Module]').attr('checked',true);
            var json_values = `{{ str_replace("@", "", $field->json_values) }}`;
            select_data('Module');
        @else
            $('#edit_form :input[name="json_type"][value=Json]').attr('checked',true);
            var json_values = @php echo (isset($field->json_values) && $field->json_values != "") ? $field->json_values : "[]"; @endphp;
            select_data('Json');
        @endif

        $('#edit_form :input[name="json_type"]').on('ifChecked',function(e){
            select_data(this.value);
        });

        var table = $("#crudTable").DataTable({
            "pageLength": "100"
        });

        function select_data(element) {
            // console.log(element);
            $(":input#json_values").html();
            if(element == "Json") {
                $(":input#json_values").attr('multiple',true);
                $(":input#json_values").attr('name',"json_values[]");
                
                var test = $(":input#json_values").select2({
                    placeholder: "Enter Json",
                    tags: json_values
                });
                
                $(':input#json_values').val(json_values);
                $(":input#json_values").trigger('change.select2');
            } else {
                $(":input#json_values").attr('multiple',false);
                $(":input#json_values").attr('name',"json_values");
                var studentSelect = $(":input#json_values").select2({
                    placeholder: "Select Module",
                    data: [{id:json_values,text:json_values}],
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
                
                $(':input#json_values').val(json_values);
                $(":input#json_values").trigger('change.select2');
            }
        }
    });
</script>
@endpush
