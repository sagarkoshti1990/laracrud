@extends('layouts.parentapp')

@section('htmlheader_title')
	Register
@endsection

@section('p_content')
<div class="row m-0 f-height-full">
    <div class="col-sm">
        <div class="row">
            <div class="col-md-4 offset-md-4 f-login">
                <div class="card-group mt-4">
                    <div class="card mx-3 p-3">
                        <div class="card-head text-center m-auto pt-3">
                            <h2 class="">Register</h2>
                            {{-- <p class="text-muted pt-3">{{ trans('base.login_title') }} {{ route('webregister.post') }}</p> --}}
                        </div>
                        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.grouped_errors', ['style_class' => 'mb-0'])
                        <div class="card-body">
                            <form class="" role="form" method="POST" action="{{ route('register.store') }}">
                                {!! csrf_field() !!}
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-user"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control {{ $errors->has('first_name') ? ' is-invalid' : '' }}" placeholder="First Name" name="first_name" value="{{ old('first_name') }}">
                                    
                                    @if ($errors->has('first_name'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('first_name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-user"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control {{ $errors->has('last_name') ? ' is-invalid' : '' }}" placeholder="Last Name" name="last_name" value="{{ old('last_name') }}">
                                    
                                    @if ($errors->has('last_name'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('last_name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-envelope"></i>
                                        </span>
                                    </div>
                                    <input type="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="Email" name="email" value="{{ old('email') }}">
                                    
                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-phone"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control {{ $errors->has('phone_no') ? ' is-invalid' : '' }}" placeholder="Phone No" name="phone_no" value="{{ old('phone_no') }}">
                                    
                                    @if ($errors->has('phone_no'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('phone_no') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-lock"></i>
                                        </span>
                                    </div>
                                    <input type="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="Password" name="password">

                                    @if ($errors->has('password'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-lock"></i>
                                        </span>
                                    </div>
                                    <input type="password" class="form-control {{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" placeholder="Password Confirmation" name="password_confirmation">

                                    @if ($errors->has('password_confirmation'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="row">
                                    <div class="col-12 text-center">
                                        <button type="submit" class="btn btn-default btn-flat">Register</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
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
