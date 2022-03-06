<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login | Byparts Motor Admin</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Icons -->

</head>

<body class="login-page" style="min-height: 496.391px;">

    @yield('content')

    <!-- REQUIRED SCRIPTS -->
    <script src="{{  asset('js/app.js') }}"></script>
</body>
</html>
