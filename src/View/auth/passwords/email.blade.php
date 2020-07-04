@extends('layouts.parentapp')

@section('htmlheader_title')
	Email
@endsection

@section('p_content')
<div class="justify-content-center d-flex">
    <div class="col-md-4">
        <div class="card-group mt-4">
            <div class="card p-4">
                <div class="card-head">
                    <p class="login-box-msg">Reset Password</p>
                </div>
                <div class="card-body p-0">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form action="{{ route('password.email') }}" name="sendEmailLinkForm" id="sendEmailLinkForm" method="post"> 
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-lg-12 no-pdd">
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
                            </div>
                            <div class="form-group col-lg-12 no-pdd">
                                <button type="submit" class="btn btn-default btn-flat">Send Password Reset Link</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection