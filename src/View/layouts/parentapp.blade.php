<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('htmlheader_title')</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="icon" href="{{ asset('public/img/icon.png') }}" type="image/png">
	<link rel="stylesheet" href="{{ asset('node_modules/admin-lte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/admin-lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    @stack('p_before_styles')
    <link rel="stylesheet" href="{{ asset('node_modules/admin-lte/dist/css/AdminLTE.min.css') }}">
    <link href="{{ asset('public/css/app.css') }}" rel="stylesheet" type="text/css" />
    @stack('p_after_styles')
</head>
<body class="sidebar-mini {{config('stlc.text_color','')}}" bsurl="{{ url('') }}" adminRoute="{{ config('stlc.route_prefix') }}">
    @yield('p_content')

    @stack('p_before_scripts')    
    <script src="{{ asset('node_modules/admin-lte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('node_modules/admin-lte/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('node_modules/admin-lte/plugins/popper/umd/popper.min.js') }}"></script>
    <script src="{{ asset('node_modules/admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('node_modules/admin-lte/dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('node_modules/admin-lte/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    @include(config('stlc.view_path.inc.alerts','stlc::inc.alerts'))
    @stack('p_after_scripts')
</body>
</html>