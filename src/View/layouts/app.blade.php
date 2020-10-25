@extends(config('stlc.stlc_modules_folder_name','stlc::').'layouts.parentapp')

@section('p_content')
    <div class="wrapper">
        <header class="main-header">
            <!-- Logo -->
            <a href="{{ url('/') }}" class="logo text-secondary d-none d-md-block">
                <span class="logo-mini">{!! config('stlc.logo_mini') !!}</span>
                <span class="logo-lg">{!! config('stlc.logo_lg') !!}</span>
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-expand navbar-dark flex-column flex-md-row bd-navbar p-0" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                    <span class="sr-only">{{ trans('base.toggle_navigation') }}</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.menu')
            </nav>
        </header>
        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.sidebar')
        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.files_manager')
        <div class="content-wrapper">
            @yield('header')
            @if(isset($crud))
                @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.grouped_errors', ['style_class' => 'mx-4 mt-4 mb-0'])
            @endif
            <section class="content">
                @yield('content')
            </section>
        </div>
        <footer class="main-footer p-2"><p class="m-0">&copy; 2020 Copyright. All rights reserved.</p></footer>
    </div>
@endsection
@push('p_before_styles')
	@stack('before_styles')
@endpush
@push('p_after_styles')
    <link href="{{ asset('node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('node_modules/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('node_modules/sweetalert2/dist/sweetalert2.css') }}">
    @stack('crud_list_styles')
    @stack('crud_fields_styles')
    @stack('after_styles')
@endpush
@push('p_before_scripts')
    @stack('before_scripts')
@endpush
@push('p_after_scripts')
    <script src="{{ asset('node_modules/admin-lte/bower_components/datatables.net/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('node_modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('node_modules/datatables.net-responsive/js/dataTables.responsive.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('node_modules/admin-lte/bower_components/morris.js/morris.min.js') }}"></script>
    <script src="{{ asset('node_modules/admin-lte/plugins/pace/pace.min.js') }}"></script>
    <script src="{{ asset('node_modules/admin-lte/bower_components/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('node_modules/sweetalert2/dist/sweetalert2.js') }}"></script>
    @stack('crud_list_scripts')
    @stack('crud_fields_scripts')
    <script src="{{ asset('public/js/customapp.js') }}"></script>
    @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.datatable_ajax')
    @stack('after_scripts')
@endpush
