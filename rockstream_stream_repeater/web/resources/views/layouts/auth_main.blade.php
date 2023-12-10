<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title') - RockStream</title>

    @yield('head-content')

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    <!-- Addons Javascript Module [First Start] -->
    <script src="{{ asset('assets/js/toggle-color-mode.js')}}"></script>
    <script defer src="{{ asset('assets/vendor/alpine.js/cdn.min.js') }}"></script>
</head>

<body class="d-flex flex-column min-vh-100 justify-content-center align-items-center bg-auth-custom-gradient">
    <div class="col-xl-4">
        <div class="card shadow-lg">
            <div class="card-body">
                <div class="text-center">
                    <img src="{{ asset('assets/img/rockstream-brand.png') }}" height="100%" width="100%">
                    <hr>
                    <h4 class="text-center fw-lighter">@yield('title')</h4>
                </div>
                {!! Session::get('auth_msg') !!}
                @yield('content')
            </div>
            <div class="card-footer text-center small">
                <div class="container">
                    Copyright &copy; {{ date('Y') }}<a href="{{ url('/') }}" class="ms-1">RockStream</a>
                    <div class="d-inline">Powered By<a href="https://github.com/sandyh90" target="_blank"
                            class="ms-1">Pickedianz</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper Important -->
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>