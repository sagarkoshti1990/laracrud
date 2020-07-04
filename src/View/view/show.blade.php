@extends('layout')

@section('content-header')
	<section class="content-header">
	  <h1>
	    {{ trans('crud.preview') }} <span>{{ $crud->label }}</span>
	  </h1>
	  <ol class="breadcrumb">
	    <li><a href="{{ url(config('stlc.route_prefix'), 'dashboard') }}">{{ trans('crud.admin') }}</a></li>
	    <li><a href="{{ url($crud->route) }}" class="text-capitalize">{{ $crud->labelPlural }}</a></li>
	    <li class="active">{{ trans('crud.preview') }}</li>
	  </ol>
	</section>
@endsection

@section('content')
	@if ($crud->hasAccess('view'))
		<a href="{{ url($crud->route) }}"><i class="fa fa-angle-double-left"></i> {{ trans('crud.back_to_all') }} <span>{{ $crud->labelPlural }}</span></a><br><br>
	@endif

	<!-- Default box -->
	  <div class="box">
	    <div class="box-header with-border">
	      <h3 class="box-title">
            {{ trans('crud.preview') }}
            <span>{{ $crud->label }}</span>
          </h3>
	    </div>
	    <div class="box-body">
	      {{ dump($entry) }}
	    </div><!-- /.box-body -->
	  </div><!-- /.box -->

@endsection


@push('after_styles')
	<link rel="stylesheet" href="{{ asset('public/vendor/lara/crud/css/crud.css') }}">
	<link rel="stylesheet" href="{{ asset('public/vendor/lara/crud/css/show.css') }}">
@endpush

@push('after_scripts')
	<script src="{{ asset('public/vendor/lara/crud/js/crud.js') }}"></script>
	<script src="{{ asset('public/vendor/lara/crud/js/show.js') }}"></script>
@endpush