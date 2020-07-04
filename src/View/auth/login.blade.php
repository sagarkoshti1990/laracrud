@extends('layouts.parentapp')

@section('htmlheader_title')
	Login
@endsection

@section('p_content')
<div class="justify-content-center d-flex">
    <div class="col-md-4">
        <div class="card-group mt-4">
            <div class="card p-4">
                <div class="card-head">
                    <h1>Login</h1>
                    <p class="login-box-msg">Sign in to start your session</p>
                </div>
                <div class="card-body p-0">
                    <form class="" role="form" method="POST" autocomplete action="{{ url(config('stlc.route_prefix').'/login') }}">
                        {!! csrf_field() !!}
                        
                        <div class="form-group">
                            <label class="control-label">E-mail Address</label>
                            <div class="input-group mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                </div>
                                <input type="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" placeholder="Email" name="email" value="{{ old('email') }}">
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback">{{ $errors->first('email') }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group mb10">
                            <label class="control-label">Password</label>
                            <div class="input-group mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fa fa-eye-slash"></i></span>
                                </div>
                                <input type="password" class="form-control f-show-password {{ $errors->has('password') ? 'is-invalid' : '' }}" placeholder="Password" name="password">
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback">{{ $errors->first('password') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="row pb5">
                            <div class="col-md-12">
                                <div class="form-group form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" name="remember" class="form-check-input">
                                        Remember Me
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-default btn-flat">Sing in</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{ url(config('stlc.route_prefix', 'admin').'/password/reset') }}" >Forgot Password</a>
                                <a href="{{ url(config('stlc.route_prefix', 'admin').'/register') }}" class="text-center pull-right">Register</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection