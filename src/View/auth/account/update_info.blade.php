@extends(config('stlc.stlc_layout_path','stlc::layouts.app'))

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
                    <li class="breadcrumb-item active">{{ trans('stlc.update_account_info') }}</li>
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
        <div id="tab-information">
            <div class="card">
                <div class="card-body list-group-flush">
                    @if(isset(auth()->user()->context()->id))
                        @displayAll($crud)
                    @else
                        <h3 class="text-center"><span class="badge bg-red text-center">No Context Found</span></h3>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('after_styles')
<style media="screen">
    .lara-profile-form .required::after {
        content: ' *';
        color: red;
    }
</style>
@endsection

@push('after_scripts')
    <script src="{{ asset('public/js/create.js') }}"></script>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $(':input[name="theme_skin"]').on('change', function(){
                console.log(this.value);
                $('body').removeClass('skin-black skin-blue skin-purple skin-red skin-yellow skin-green skin-blue-light skin-black-light skin-purple-light skin-green-light skin-red-light skin-yellow-light')
                $('body').addClass(this.value);
            });
        });
    </script>
@endpush