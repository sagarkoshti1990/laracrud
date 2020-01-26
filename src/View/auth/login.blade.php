@extends('stlcauth.layout.app')

@section('htmlheader_title')
	{{-- Setting::value('COMPANY_NAME','Company') --}} - Login
@endsection

@section('content')
<div class="login-box">
    <div class="login-logo">
        <b>Welcome to</b> {{-- Setting::value('COMPANY_NAME','PeCfy') --}}
    </div>
    <!-- /.login-logo -->
    <div class="box">
        <div class="box-header with-border">
            <div class="with-border pt5" style="display:flex;align-items:center;justify-content:center;">
                {{-- <img src="{{ Setting::value('COMPANY_LOGO') }}" class="img-responsive"> --}}
            </div>
        </div>
        <div class="register-box-body pt5">
            <p class="login-box-msg">Sign in to start your session</p>
            <form class="" role="form" method="POST" action="{{ url(config('lara.base.route_prefix').'/login') }}">
                {!! csrf_field() !!}
                
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} has-feedback">
                    <label class="control-label">{{ trans('base.email_address') }}</label>
                    <input type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}">
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
                
                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }} has-feedback">
                    <label class="control-label">{{ trans('base.password') }}</label>
                    <input type="password" class="form-control" placeholder="Password" name="password">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="row pb5">
                    <div class="col-xs-8">
                        {{-- <div class="checkbox icheck">
                            <label>
                                <input type="checkbox" name="remember"> {{ trans('base.remember_me') }}
                            </label>
                        </div> --}}
                        <a href="{{ url(config('lara.base.route_prefix', 'admin').'/password/reset') }}" >{{ trans('base.forgot_your_password') }}</a>
                    </div>
                    <div class="col-xs-4 pull-right">
                        <button type="submit" class="btn bg-orange btn-flat">
                            {{ trans('base.sign_in') }}
                        </button>
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="col-md-12">
                        <a href="{{ url(config('lara.base.route_prefix', 'admin').'/password/reset') }}" >{{ trans('base.forgot_your_password') }}</a>
                        <a href="{{ url(config('lara.base.route_prefix', 'admin').'/register') }}" class="text-center pull-right">Trial Account</a>
                    </div>
                </div> --}}
            </form>
        </div>
        <div class="social-auth-links text-center">
        </div>

        {{--  <div class="social-auth-links text-center">
        <p>- OR -</p>
        <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using
            Facebook</a>
        <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign in using
            Google+</a>
        </div>  --}}
        <!-- /.social-auth-links -->

        {{--  <a href="#">I forgot my password</a><br>
        <a href="register.html" class="text-center">Register a new membership</a>  --}}

    </div>
</div>
@endsection
