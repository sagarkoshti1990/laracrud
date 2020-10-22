@extends(config('stlc.stlc_layout_path','stlc::layouts.app'))
@section('header')
<section class="content-header">
    <h1>
        <a href="{{ url($crud->route) }}">
            <span class="{{ $crud->icon }}"></span>
            <span class="text-capitalize">{{ $crud->label }}</span>
        </a>
        <small>Edit</small>
    </h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url(config('stlc.route_prefix'), 'dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ url($crud->route) }}" class="text-capitalize">{{ $crud->labelPlural }}</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
</section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(array('url' => $crud->route.'/'.$module->id, 'method' => 'put', 'id' => 'edit_form', "autocomplete"=>"off")) !!}
                <div class="box">
                    {{-- <div class="box-header with-border">
                        <h4 class="box-title">{{ trans('stlc.edit') }}</h4>
                    </div> --}}
                    <div class="box-body">
                        @if(isset($src))
                            {{ Form::hidden('src', $src) }}
                        @endif
                        @form($crud, [], ["class" => "col-md-6"])
                    </div><!-- /.box-body -->
                    <div class="box-footer">
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
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#edit_form').validate();
        });
    </script>
@endpush