@extends(config('stlc.stlc_layout_path','stlc::layouts.app'))

@section('header')
    <section class="content-header">
        <div class="row">
            <div class="col-md-6">
                <h1>
                    <span class="fa {{ $crud->icon }}"></span>
                    <span class="text-capitalize">{{ $crud->labelPlural }}</span>
                    <small>{{ trans('stlc.all') }} <span>{{ $crud->labelPlural }}</span> {{ trans('stlc.in_the_database') }}.</small>
                </h1>
            </div>
            <div class="col-md-6"></div>
        </div>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                @include('crud.inc.button_stack', ['stack' => 'top'])
                {{-- <div class="card-header {{ $crud->hasAccess('create')?'with-border':'d-none' }}">
                </div> --}}
                <div class="card-body">
                    <table id="crudTable" class="{{ config('stlc.css.table','table display crudTable') }}">
                        <thead class="{{ config('stlc.css.thead','thead-light') }}">
                            <tr>
                                {{-- Table columns --}}
                                @foreach ($crud->columns as $column)
                                <th>{{ $column['label'] }}</th>
                                @endforeach

                                @if ($crud->buttons->where('stack', 'line')->count() )
                                <th style="width: 100px;">{{ trans('stlc.actions') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($settings as $setting)
                                @php
                                    $crud->row = $setting;
                                @endphp
                                <tr>
                                    <td>{{ $setting->key }}</td>
                                    <td>{!! \FormBuilder::get_field_value($crud, 'value', collect(config('lara.base.setting_keys'))->where('key',$setting->key)->first()['type']) !!}</td>
                                    <td>
                                        <a href="{{ url($crud->route.'/'.$setting->id.'/edit') }}" class="btn btn-warning btn-sm btn-flat" data-toggle="tooltip" title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div><!-- /.card-body -->
            </div><!-- /.card -->
        </div>
    </div>

    <div class="modal" tabindex="-1" role="dialog" id="add_modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(array('url' => $crud->route, 'method' => 'post', 'id' => 'add_form', "autocomplete"=>"off")) !!}
                    <div class="modal-header">
                        <h5 class="modal-title">{{ trans('stlc.add_a_new') }} {{ $crud->label }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if(isset($src))
                            {{ Form::hidden('src', $src) }}
                        @endif
                        @form($crud,[],['col' => '1'])
                    </div>
                    <div class="modal-footer">
                        @include('crud.inc.form_save_buttons',['model_close' => 'add_modal'])
                    </div>
                {!! Form::close() !!}
            </div>
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
                "pageLength": "10",
                /* Disable initial sort */
                "aaSorting": [],
                "processing": true,
                dom: "<'row'<'col-sm-6'l><'col-sm-6'f>><'table-responsive'tr><'row'<'col-sm-5'i><'col-sm-7'p>>",
            });
        });
    </script>
@endpush
