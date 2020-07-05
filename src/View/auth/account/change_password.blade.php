@extends(config('stlc.stlc_modules_folder_name','stlc::').'layouts.app')

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
            <a href="{{ url('/') }}"></a>
        </li>
        <li>
            <a href="{{ route('stlc.account.info') }}">{{ trans('base.my_account') }}</a>
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
        @include(config('stlc.stlc_modules_folder_name','stlc::').'auth.account.sidemenu')
    </div>
    <div class="col-md-9">
        <form class="form" action="{{ route('stlc.account.password') }}" method="post">
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
                                @php $label = 'Old Password'; $field = 'old_password'; @endphp
                                <label class="required">{{ $label }}</label>
                                <input autocomplete="new-password" required class="form-control"
                                    type="password" name="{{ $field }}"
                                    id="{{ $field }}" value="" placeholder="{{ $label }}">
                            </div>
                            <div class="form-group">
                                @php $label = 'New Password'; $field = 'new_password'; @endphp
                                <label class="required">{{ $label }}</label>
                                <input autocomplete="new-password" required class="form-control"
                                    type="password" name="{{ $field }}"
                                    id="{{ $field }}" value="" placeholder="{{ $label }}">
                            </div>
                            <div class="form-group">
                                @php $label = 'Confirm Password'; $field = 'confirm_password'; @endphp
                                <label class="required">{{ $label }}</label>
                                <input autocomplete="new-password" required class="form-control"
                                    type="password" name="{{ $field }}"
                                    id="{{ $field }}" value="" placeholder="{{ $label }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer p15 pt0">
                    <button type="submit" class="btn bg-orange pull-right btn-flat btn-labeled p5">Change Password</button>
                    <a href="{{ url(config('stlc.route_prefix')) }}" class="btn p5 btn-default pull-left btn-flat btn-labeled">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
