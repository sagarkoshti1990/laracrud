<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('htmlheader_title')</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	@stack('p_before_styles')
    <link rel="stylesheet" href="{{ asset('node_modules/bootstrap/dist/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/admin-lte/bower_components/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/admin-lte/bower_components/Ionicons/css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/admin-lte/dist/css/AdminLTE.min.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/admin-lte/dist/css/skins/_all-skins.min.css') }}">
    <link rel="stylesheet" href="{{ asset('node_modules/admin-lte/plugins/iCheck/all.css') }}">
	@stack('p_after_styles')
</head>
<body class="{{ config('stlc.skin') }} sidebar-mini hold-transition login-page" bsurl="{{ url('') }}" adminRoute="{{ config('stlc.route_prefix') }}">
    @yield('p_content')

    @stack('p_before_scripts')    
    <script src="{{ asset('node_modules/admin-lte/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('node_modules/admin-lte/bower_components/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('node_modules/popper.js/dist/umd/popper.js') }}"></script>
    <script src="{{ asset('node_modules/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('node_modules/admin-lte/bower_components/morris.js/morris.min.js') }}"></script>
    <script src="{{ asset('node_modules/admin-lte/bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('node_modules/admin-lte/bower_components/fastclick/lib/fastclick.js') }}"></script>
    <script src="{{ asset('node_modules/admin-lte/dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('node_modules/jquery-validation/dist/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('node_modules/admin-lte/plugins/iCheck/icheck.min.js') }}"></script>
    @stack('p_after_scripts')
</body>
</html>