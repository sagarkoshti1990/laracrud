@extends(config('stlc.view_path.layouts.parentapp','stlc::layouts.parentapp'))

@section('htmlheader_title')
	Email
@endsection

@section('p_content')
<div class="login-page" style="min-height:100vh;height:auto;">
    <div class="login-box">
        <div class="card">
            <div class="login-logo mt-3 mb-1">
                <p class="login-box-msg">Reset Password</p>
            </div>
            <div class="card-body login-card-body">
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
                                    <input type="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" placeholder="Email" name="email" value="{{ old('email') }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                    </div>
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
@endsection