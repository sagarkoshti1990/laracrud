@extends(config('stlc.view_path.layouts.parentapp','stlc::layouts.parentapp'))

@section('p_content')
    <div class="wrapper">
            <!-- Logo -->
            {{-- <a href="{{ url('/') }}" class="logo text-secondary d-none d-md-block">
                <span class="logo-mini">{!! config('stlc.logo_mini') !!}</span>
                <span class="logo-lg">{!! config('stlc.logo_lg') !!}</span>
            </a> --}}
        @include(config('stlc.view_path.inc.menu','stlc::inc.menu'))
        @include(config('stlc.view_path.inc.sidebar','stlc::inc.sidebar'))
        @include(config('stlc.view_path.inc.files_manager','stlc::inc.files_manager'))
        <div class="content-wrapper">
            @yield('header')
            @if(isset($crud))
                @include(config('stlc.view_path.inc.grouped_errors','stlc::inc.grouped_errors'), ['style_class' => 'mx-4 mt-4 mb-0'])
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
    @stack('crud_list_styles')
    @stack('crud_fields_styles')
@endpush
@push('p_after_styles')
    <link href="{{ asset('node_modules/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('node_modules/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('node_modules/admin-lte/plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/admin-lte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    @stack('after_styles')
@endpush
@push('p_before_scripts')
    @stack('before_scripts')
@endpush
@push('p_after_scripts')
    <script src="{{ asset('node_modules/admin-lte/plugins/datatables/jquery.dataTables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('node_modules/admin-lte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('node_modules/admin-lte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('node_modules/admin-lte/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('node_modules/admin-lte/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    @stack('crud_list_scripts')
    @stack('crud_fields_scripts')
    <script src="{{ asset('public/js/customapp.js') }}"></script>
    
    @include(config('stlc.view_path.inc.datatable_ajax','stlc::inc.datatable_ajax'))
    @stack('after_scripts')
    <script>
        var current_url = "{{ Request::fullUrl() }}";
        var full_url = current_url + location.search;
        var $navLinks = $("ul.nav-sidebar li a");
        // First look for an exact match including the search string
        var $curentPageLink = $navLinks.filter(
            function () {
                return $(this).attr('href') === full_url;
            }
        );
        // If not found, look for the link that starts with the url
        if (!$curentPageLink.length > 0) {
            $curentPageLink = $navLinks.filter(
                function () {
                    return (current_url != "{{ url(config('stlc.route_prefix', 'admin')) }}" && current_url != "{{ url('/') }}" && $(this).attr('href').startsWith(current_url)) || current_url.startsWith($(this).attr('href'));
                }
            );
        }
        $curentPageLink.closest('li.has-treeview').addClass('menu-open');
        $curentPageLink.closest('li.has-treeview').find('a').first().addClass('active');
        $curentPageLink.closest('li').find('a').addClass('active');
    </script>
@endpush
