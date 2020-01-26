@extends('stlcauth.layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-6">
                <div class="cmp-info">
                    <div class="cm-logo">
                        <img src="{{ asset('public/assets/images/logo.png') }}" alt="DrQlik_HLD" class="img-responsive" style="width:120px;">
                        <p>DrQlik, is a social networking platform for healthcare professionals.</p>
                    </div><!--cm-logo end-->			
                </div><!--cmp-info end-->
            </div>
        <div class="col-lg-6">
            <div class="login-sec">
                <ul class="sign-control">
                    
                </ul>			
                <div class="sign_in_sec current" id="tab-1"> 
                    <h3>Finally, Choose a new password</h3>
                    <h4>Please enter your email</h4>
                    <form class="form-horizontal" method="POST" action="{{ url('password/reset') }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="form-group sn-field {{ $errors->has('email') ? ' has-error' : '' }}">
                            {{-- <label for="email" class="col-md-4 control-label">E-Mail Address</label> --}}
                            <input id="hidden" type="email" class="form-control" name="email" readonly value="{{ $email ?? old('email') }}" required autofocus>
                            <i class="la la-envelope"></i>
                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group sn-field {{ $errors->has('password') ? ' has-error' : '' }}">
                            {{-- <label for="password" class="col-md-4 control-label">Password</label> --}}
                            <input id="password" type="password" class="form-control" name="password" required placeholder="Password">
                            <i class="la la-key"></i>
                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group sn-field {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            {{-- <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label> --}}
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required placeholder="Confirm Password">
                            <i class="la la-key"></i>
                            @if ($errors->has('password_confirmation'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn bg-orange btn-flat">
                                Reset Password
                            </button>
                        </div>
                    </form>
                </div><!--sign_in_sec end-->		
            </div>
        </div>
    </div>
@endsection

@push('afterStyles')
<style>
    .cmp-info{
        padding: 70px 5px 30px 5px;
    }    
    .sign-in-page{
        padding: 50px 0 20px 0;
    }
</style>    
@endpush
