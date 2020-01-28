@extends('layout')

@section('header')
  <section class="content-header">
    <h1>
      <span>{{ ucfirst($crud->label) }}</span> {{ trans('crud.revisions') }}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ url(config('stlc.route_prefix'),'dashboard') }}">{{ trans('crud.admin') }}</a></li>
      <li><a href="{{ url($crud->route) }}" class="text-capitalize">{{ $crud->labelPlural }}</a></li>
      <li class="active">{{ trans('crud.revisions') }}</li>
    </ol>
  </section>
@endsection

@section('content')
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <!-- Default box -->
    @if ($crud->hasAccess('view'))
      <a href="{{ url($crud->route) }}"><i class="fa fa-angle-double-left"></i> {{ trans('crud.back_to_all') }} <span>{{ $crud->labelPlural }}</span></a><br><br>
    @endif

    @if(!count($revisions))
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">{{ trans('crud.no_revisions') }}</h3>
        </div>
      </div>
    @else
      @include('crud.inc.revision_timeline')
    @endif
  </div>
</div>
@endsection


@push('after_styles')
  <link rel="stylesheet" href="{{ asset('public/vendor/lara/crud/css/crud.css') }}">
  <link rel="stylesheet" href="{{ asset('public/vendor/lara/crud/css/revisions.css') }}">
@endpush

@push('after_scripts')
  <script src="{{ asset('public/vendor/lara/crud/js/crud.js') }}"></script>
  <script src="{{ asset('public/vendor/lara/crud/js/revisions.js') }}"></script>
@endpush