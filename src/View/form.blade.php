@extends('layouts.app')
@section('header')
    @php
        $route = isset($crud->row->id) ? $crud->route.'/'.$crud->row->id : $crud->route;
        $method = isset($crud->row->id) ? 'put' : 'post';
        $form_id = isset($crud->row->id) ? 'edit_form' : 'add_form';
        $add_edit = isset($crud->row->id) ? trans('crud.edit') : trans('crud.add_a_new')
    @endphp
    <section class="content-header">
        <h1 class="floating-box md-pr-5">
            <a href="{{ url($crud->route) }}">
            <span class="fa {{ $crud->icon }}"></span><span>{{ $crud->label }}</span></a>
            <small><i class="fa fa-angle-double-right"></i> {{ trans('crud.add') }}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url(config('lara.base.route_prefix'), 'dashboard') }}">{{ trans('crud.admin') }}</a></li>
            <li><a href="{{ url($crud->route) }}" class="text-capitalize">{{ $crud->labelPlural }}</a></li>
            <li class="active">{{ $add_edit }}</li>
        </ol>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['url' => $route, 'method' => $method, 'id' => $form_id]) !!}
                <div class="box">
                    <div class="box-header">
                        <h4 class="box-title">{{ $add_edit }} {{ $crud->label }}</h4>
                    </div>
                    <div class="box-body">
                        @if(isset($src))
                            {{ Form::hidden('src', $src) }}
                        @endif
                        @form($crud, [], ["class" => "col-md-6"])
                    </div>
                    <div class="box-footer">
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
    <script src="{{ asset('public/js/create.js') }}"></script>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#{{$form_id}}').validate();
        });
    </script>
@endpush