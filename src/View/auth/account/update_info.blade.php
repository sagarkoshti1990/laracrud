@extends(config('stlc.stlc_layout_path','stlc::layouts.app'))

@section('header')
<section class="content-header">
    <h1>My Account</h1>
    <ol class="breadcrumb">
        <li>
            <a href="{{ url('/') }}"></a>
        </li>
        <li>
            <a href="{{ route('stlc.account.info') }}">My Account</a>
        </li>
        <li class="active">Update Account Info</li>
    </ol>
</section>
@endsection

@section('content')
<div class="row">
    <div class="col-md-3">
        @include(config('stlc.stlc_modules_folder_name','stlc::').'auth.account.sidemenu')
    </div>
    <div class="col-md-9">
        <div id="tab-information">
            <div class="box">
                <div class="box-body">
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