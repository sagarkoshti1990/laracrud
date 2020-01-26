@extends('layouts.app')

@section('header')
<section class="content-header">
    <h1>
        {{ trans('base.my_account') }}
    </h1>
    <ol class="breadcrumb">
        <li>
            <a href="{{ url('/') }}">{{-- Setting::value('COMPANY_NAME','Company') --}}</a>
        </li>
        <li>
            <a href="{{ route('lara.account.info') }}">{{ trans('base.my_account') }}</a>
        </li>
        <li class="active">
            {{ trans('base.update_account_info') }}
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
        <div id="tab-information">
            <div class="box">
                <div class="box-body">
                    @if(isset(auth()->user()->context()->id))
                        @displayAll($crud)
                    @else
                        <h1 class="text-center"><span class="label large bg-red text-center">No Context Found</span></h1>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('after_styles')
<style media="screen">
    .lara-profile-form .required::after {
        content: ' *';
        color: red;
    }
</style>
@endsection

@push('after_scripts')
    <script src="{{ asset('public/js/create.js') }}"></script>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $(':input[name="theme_skin"]').on('change', function(){
                console.log(this.value);
                $('body').removeClass('skin-black skin-blue skin-purple skin-red skin-yellow skin-green skin-blue-light skin-black-light skin-purple-light skin-green-light skin-red-light skin-yellow-light')
                $('body').addClass(this.value);
            });
        });
    </script>
@endpush