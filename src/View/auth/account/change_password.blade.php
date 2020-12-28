@extends(config('stlc.stlc_layout_path','stlc::layouts.app'))

@section('after_styles')
<style media="screen">
    .lara-profile-form .required::after {
        content: ' *';
        color: red;
    }
</style>
@endsection

@section('header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>
                    <span class="far fa-id-card"></span>
                    <span class="text-capitalize">{{ trans('stlc.my_account') }}</span>
                </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url(config('stlc.route_prefix'), 'dashboard') }}">{{ trans('stlc.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('stlc.account.info') }}" class="text-capitalize">{{ trans('stlc.my_account') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('stlc.change_password') }}</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-3">
        @include(config('stlc.view_path.auth.account.sidemenu','stlc::auth.account.sidemenu'))
    </div>
    <div class="col-md-9">
        <form class="form" action="{{ route('stlc.account.password') }}" method="post">
            {!! csrf_field() !!}
            <div class="card">
                <div class="card-body lara-profile-form">
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
                <div class="card-footer">
                    <button type="submit" class="btn bg-orange float-right btn-flat btn-labeled p5">Change Password</button>
                    <a href="{{ url(config('stlc.route_prefix')) }}" class="btn p5 btn-default float-left btn-flat btn-labeled">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
