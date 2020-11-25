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
                    <small>{{trans('stlc.add')}}</small>
                </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url(config('stlc.route_prefix'), 'dashboard') }}">{{trans('stlc.dashboard')}}</a></li>
                    <li class="breadcrumb-item"><a href="{{ url($crud->route) }}" class="text-capitalize">{{ $crud->labelPlural }}</a></li>
                    <li class="breadcrumb-item active">{{trans('stlc.add')}}</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(array('url' => $crud->route, 'method' => 'post', 'id' => 'add_form', "autocomplete"=>"off")) !!}
                <div class="card">
                    {{-- <div class="card-header with-border">
                        <h4 class="card-title">{{ $crud->label }}</h4>
                    </div> --}}
                    <div class="card-body">
                        @if(isset($src))
                            {{ Form::hidden('src', $src) }}
                        @endif
                        @form($crud, [], ["class" => "col-md-6"])
                    </div>
                    <div class="card-footer">
                        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.form_save_buttons')
                    </div>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@push('after_styles')
    
@endpush

@push('after_scripts')
    <script src="{{ asset('public/js/create.js') }}"></script>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#add_form').validate();
        });
    </script>
@endpush