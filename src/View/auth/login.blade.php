@extends(config('stlc.stlc_modules_folder_name','stlc::').'layouts.parentapp')

@section('htmlheader_title')
	Login
@endsection

@section('p_content')
<div class="login-page" style="min-height:100vh;height:auto;">
    <div class="login-box">
        <div class="card">
            <div class="login-logo mt-3 mb-1">
                <a href="../../index2.html"><b>Login</b></a>
            </div>
            <div class="card-body login-card-body">
                <p class="login-box-msg">Sign in to start your session</p>
                <form class="" role="form" method="POST" autocomplete action="{{ url(config('stlc.route_prefix').'/login') }}">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label class="control-label">E-mail Address</label>
                        <div class="input-group mb-3">
                            <input type="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" placeholder="Email" name="email" value="{{ old('email') }}">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                            </div>
                            @if ($errors->has('email'))
                                <span class="invalid-feedback">{{ $errors->first('email') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group mb10">
                        <label class="control-label">Password</label>
                        <div class="input-group mb-3">
                            <input type="password" class="form-control f-show-password {{ $errors->has('password') ? 'is-invalid' : '' }}" placeholder="Password" name="password">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fa fa-eye-slash"></i></span>
                            </div>
                            @if ($errors->has('password'))
                                <span class="invalid-feedback">{{ $errors->first('password') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 form-group">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember">
                                <label for="remember">Remember Me</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="form-group btn btn-default btn-block btn-flat">Sign In</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-1">
                            <a href="{{ url(config('stlc.route_prefix', 'admin').'/password/reset') }}" >Forgot Password</a>
                        </div>
                        <div class="col-md-12">
                            <a href="{{ url(config('stlc.route_prefix', 'admin').'/register') }}" class="text-center">Register</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection