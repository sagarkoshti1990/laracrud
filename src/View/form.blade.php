@extends(config('stlc.stlc_layout_path','stlc::layouts.app'))
@section('header')
    @php
        $route = (isset($crud->row->id) && !isset($_GET['copy'])) ? $crud->route.'/'.$crud->row->id : $crud->route;
        $method = (isset($crud->row->id) && !isset($_GET['copy'])) ? 'put' : 'post';
        $form_id = (isset($crud->row->id) && !isset($_GET['copy'])) ? 'edit_form' : 'add_form';
        $add_edit = (isset($crud->row->id) && !isset($_GET['copy'])) ? trans('stlc.edit') : trans('stlc.add');
    @endphp
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <a href="{{ url($crud->route) }}">
                            <span class="{{ $crud->icon }}"></span>
                            <span class="text-capitalize">{{ $crud->label }}</span>
                        </a>
                        <small>{{ $add_edit }}</small>
                    </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url(config('stlc.route_prefix'), 'dashboard') }}">{{ trans('stlc.dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ url($crud->route) }}" class="text-capitalize">{{ $crud->labelPlural }}</a></li>
                        <li class="breadcrumb-item active">{{ $add_edit }}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        {!! Form::open(['url' => $route, 'method' => $method, 'id' => $form_id]) !!}
            <div class="card">
                {{-- <div class="card-header">
                    <h4 class="card-title">{{ $add_edit }} {{ $crud->label }}</h4>
                </div> --}}
                <div class="card-body">
                    @if(isset($src))
                        {{ Form::hidden('src', $src) }}
                    @endif
                    @form($crud, [], ["class" => "col-md-6"])
                </div>
                <div class="card-footer">
                    @include(config('stlc.view_path.inc.form_save_buttons','stlc::inc.form_save_buttons'))
                </div>
            </div>
        {!! Form::close() !!}
    </div>
</div>
@endsection
@push('after_scripts')
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#{{$form_id}}').validate();
        });
    </script>
@endpush