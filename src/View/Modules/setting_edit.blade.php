@extends(config('stlc.stlc_layout_path','stlc::layouts.app'))
@section('header')
    <section class="content-header">
        <div class="row">
            <div class="col-md-6">
                <h1>
                    <a href="{{ url($crud->route) }}">
                        <span class="fa {{ $crud->icon }} mr-1"></span><span>{{ $crud->label }}</span>
                    </a>
                    <small><i class="fa fa-angle-right"></i> {{ trans('stlc.edit') }}</small>
                </h1>
            </div>
            <div class="col-md-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="{{ url(config('lara.base.route_prefix'), 'dashboard') }}">{{ trans('stlc.admin') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ url($crud->route) }}" class="text-capitalize">{{ $crud->labelPlural }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('stlc.edit') }}</li>
                </ol>
            </div>
        </div>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(array('url' => $crud->route.'/'.$setting->id, 'method' => 'put', 'id' => 'edit_form', "autocomplete"=>"off")) !!}
                <div class="card">
                    {{-- <div class="card-header with-border">
                        <h4 class="card-title">{{ trans('stlc.edit') }}</h4>
                    </div> --}}
                    <div class="card-body">
                        @if(isset($src))
                            {{ Form::hidden('src', $src) }}
                        @endif
                        <div class="col-md-6">
                            @form($crud, [], ["col" => "1"])
                        </div>
                    </div><!-- /.card-body -->
                    <div class="card-footer">
                        @include('crud.inc.form_save_buttons')
                    </div>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@push('after_styles')
    
@endpush

@push('after_scripts')
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#edit_form').validate();
        });
    </script>
@endpush