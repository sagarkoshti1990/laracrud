@extends('layouts.app')

@section('after_styles')
<style media="screen">
    .lara-profile-form .required::after {
        content: ' *';
        color: red;
    }
</style>
@endsection

@section('header')
<section class="content-header">
    <h1>
        {{ trans('base.my_account') }}
    </h1>
    <ol class="breadcrumb">
        <li>
            <a href="{{ url('/') }}">{{ Setting::value('COMPANY_NAME','Company') }}</a>
        </li>
        <li>
            <a href="{{ route('pecfy.account.info') }}">{{ trans('base.my_account') }}</a>
        </li>
        <li class="active">
            {{ trans('base.change_password') }}
        </li>
    </ol>
</section>
@endsection

@section('content')
<div class="row">
    <div class="col-md-3">
        @include('auth.account.sidemenu')
    </div>
    <div class="col-md-9">
        <form class="form" action="{{ route('pecfy.account.password') }}" method="post">
            {!! csrf_field() !!}
            <div class="box">
                <div class="box-body p15 pb5 pt10 lara-profile-form">

                    <div class="row">
                        <div class="col-md-12">
                            @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                            @endif
                            @if ($errors->count())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            <div class="form-group">
                                @php
                                $label = trans('base.old_password');
                                $field = 'old_password';
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <input autocomplete="new-password" required class="form-control" type="password" name="{{ $field }}"
                                    id="{{ $field }}" value="" placeholder="{{ $label }}">
                            </div>
                            <div class="form-group">
                                @php
                                $label = trans('base.new_password');
                                $field = 'new_password';
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <input autocomplete="new-password" required class="form-control" type="password" name="{{ $field }}"
                                    id="{{ $field }}" value="" placeholder="{{ $label }}">
                            </div>
                            <div class="form-group">
                                @php
                                $label = trans('base.confirm_password');
                                $field = 'confirm_password';
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <input autocomplete="new-password" required class="form-control" type="password" name="{{ $field }}"
                                    id="{{ $field }}" value="" placeholder="{{ $label }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer p15 pt0">
                    <button type="submit" class="btn bg-orange pull-right btn-flat btn-labeled p5">
                      
                        {{ trans('base.change_password') }}
                    </button>
                    <a href="{{ url(config('stlc.route_prefix')) }}" class="btn p5 btn-default pull-left btn-flat btn-labeled">
                        {{ trans('base.cancel') }}
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
