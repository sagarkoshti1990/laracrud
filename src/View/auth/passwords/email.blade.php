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
                    <h3>First, let's find your account</h3>
                    <h4>Please enter your email</h4>
                    <form action="{{ route('password.email') }}" name="sendEmailLinkForm" id="sendEmailLinkForm" method="post"> 
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-lg-12 no-pdd">
                                <div class="form-group sn-field {{ $errors->has('email') ? ' has-error' : '' }}">
                                    {{-- <label for="email">Email:</label> --}}
                                    <input type="text" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" autocomplete="off">
                                    <i class="la la-envelope"></i>
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div><!--sn-field end-->
                            </div>
                            <div class="form-group col-lg-12 no-pdd">
                                <button type="submit" class="btn btn-primary">Find Account</button>
                            </div>
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