@extends(config('stlc.stlc_modules_folder_name','stlc::').'layouts.parentapp')

@section('htmlheader_title')
	Register
@endsection

@section('p_content')
<div class="register-page" style="min-height:100vh;height:auto;">
    <div class="register-box">
        <div class="card">
            <div class="register-logo mt-3 mb-1">
                <a href="../../index2.html"><b>Register</b></a>
            </div>
            <div class="card-body register-card-body">
                <p class="login-box-msg">Register a new membership</p>
                @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.grouped_errors', ['style_class' => 'mb-0'])
                <form class="" role="form" method="POST" action="{{ route('register.store') }}">
                    {!! csrf_field() !!}
                    <div class="input-group mb-3">
                        <input type="text" class="form-control {{ $errors->has('first_name') ? ' is-invalid' : '' }}" placeholder="First Name" name="first_name" value="{{ old('first_name') }}">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fa fa-user"></i>
                            </span>
                        </div>
                        
                        @if ($errors->has('first_name'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('first_name') }}</strong>
                            </span>
                        @endif
                    </div>
                    
                    <div class="input-group mb-3">
                        <input type="text" class="form-control {{ $errors->has('last_name') ? ' is-invalid' : '' }}" placeholder="Last Name" name="last_name" value="{{ old('last_name') }}">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fa fa-user"></i>
                            </span>
                        </div>
                        
                        @if ($errors->has('last_name'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('last_name') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="input-group mb-3">
                        <input type="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="Email" name="email" value="{{ old('email') }}">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fa fa-envelope"></i>
                            </span>
                        </div>
                        
                        @if ($errors->has('email'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                    
                    <div class="input-group mb-3">
                        <input type="text" class="form-control {{ $errors->has('phone_no') ? ' is-invalid' : '' }}" placeholder="Phone No" name="phone_no" value="{{ old('phone_no') }}">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fa fa-phone"></i>
                            </span>
                        </div>
                        
                        @if ($errors->has('phone_no'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('phone_no') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="input-group mb-3">
                        <input type="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="Password" name="password">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fa fa-lock"></i>
                            </span>
                        </div>

                        @if ($errors->has('password'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control {{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" placeholder="Password Confirmation" name="password_confirmation">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fa fa-lock"></i>
                            </span>
                        </div>

                        @if ($errors->has('password_confirmation'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                            </span>
                        @endif
                    </div>
                    
                    <div class="row">
                        <div class="col-12 text-center">
                            <button type="submit" class="form-group btn btn-default btn-block btn-flat">Register</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <a href="{{ url(config('stlc.route_prefix', 'admin').'/login') }}" class="text-center">I already have a membership</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('after_styles')
    <style>
        .card,input{
            box-shadow: 0 0 5PX #d9d9d9;
        }
    </style>
@endpush
