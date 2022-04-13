<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title') - {{ config('app.name') }}</title>

    @yield('head-content')

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="{{ asset('assets/vendor/bootstrap-5.0.2/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    <!-- Addons CSS Module -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/datatables/dataTables.bootstrap5.min.css') }}">
    <link href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">

    <!-- Addons Javascript Module [First Start] -->
    <script defer src="{{ asset('assets/vendor/alpine.js/cdn.min.js') }}"></script>

</head>

<body class="d-flex flex-column min-vh-100">
    <div class="wrapper flex-grow-1">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <button class="btn btn-outline" type="button" data-bs-toggle="offcanvas"
                    data-bs-target=".sidebar-toggle">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <a class="navbar-brand" href="{{ route('home') }}">{{ config('app.name') }}</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                    <div class="navbar-nav ms-auto">
                        <div class="nav-item dropdown mx-md-2">
                            <div class="dropdown-toggle indicator-rtmp" data-bs-toggle="dropdown" aria-expanded="false"
                                type="button">
                                Status Streaming: <span class="status-stream badge bg-secondary"><span
                                        class="bi bi-arrow-down-up me-1"></span>
                                    <div class="spinner-border spinner-border-sm" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </span>
                            </div>
                            <div class="dropdown-menu dropdown-menu-end text-small">
                                <div class="dropdown-item-text indicator-rtmp"><span
                                        class="bi bi-speedometer2 me-1"></span>Bandwidth:
                                    <span class="bandwidth">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </span>
                                </div>
                                <div class="dropdown-item-text indicator-rtmp"><span
                                        class="bi bi-arrow-down-up me-1"></span>Bytes:
                                    <span class="bytes-in-out">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </span>
                                </div>
                                <div class="dropdown-item-text indicator-rtmp"><span
                                        class="bi bi-clock me-1"></span>Uptime:
                                    <span class="uptime">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="nav-item dropdown">
                            <div class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" type="button">
                                <span class="bi bi-person-circle me-1"></span>Hello,<span class="ms-1">{{
                                    \Str::of(Auth::user()->name)->limit(13); }}</span>
                            </div>
                            <div class="dropdown-menu dropdown-menu-end text-small">
                                <div class="dropdown-item-text text-center small">Logged in
                                    {{\Carbon\Carbon::createFromTimeStamp(strtotime(Auth::user()->last_login))->diffForHumans()}}
                                </div>
                                <div class="dropdown-item text-reset" role="button" data-bs-toggle="modal"
                                    data-bs-target=".modal-about-display">
                                    <span class="bi bi-info-circle me-1"></span>About
                                </div>
                                <hr class="dropdown-divider">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><span
                                            class="bi bi-box-arrow-right me-1"></span>Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="offcanvas offcanvas-start sidebar-toggle sidebar" tabindex="-1" data-bs-backdrop="true">
            <div class="offcanvas-body">
                <div class="h4 d-inline"><img src="{{ asset('assets/img/rockstream-logo.png') }}" height="50" width="50"
                        class="p-1">{{
                    config('app.name') }}</div>
                <button type="button" class="btn btn-outline sidebar-btn" data-bs-dismiss="offcanvas">
                    <span class="sidebar-toggler-icon"></span>
                </button>
                <hr>
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="{{ route('home') }}" class="nav-link text-reset" aria-current="page">
                            <span class="bi bi-house fs-4 me-1"></span>Home
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('stream.home') }}" class="nav-link text-reset">
                            <span class="bi bi-hdmi fs-4 me-1"></span>Input Stream
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('premiere.home') }}" class="nav-link text-reset">
                            <span class="bi bi-play-btn fs-4 me-1"></span>Premiere Video
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.settings') }}" class="nav-link text-reset">
                            <span class="bi bi-person-circle fs-4 me-1"></span>Account Settings
                        </a>
                    </li>
                    @if(Auth::user()->is_operator == TRUE)
                    <hr>
                    <div class="fs-5 fw-light"><span class="bi bi-wrench-adjustable-circle me-1"></span>Administrator
                    </div>
                    <li>
                        <a href="{{ route('analytics.home') }}" class="nav-link text-reset">
                            <span class="bi bi-activity fs-4 me-1"></span>Livestream Analytics
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('users.home') }}" class="nav-link text-reset">
                            <span class="bi bi-people fs-4 me-1"></span>Management Users
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('interfaces.home') }}" class="nav-link text-reset">
                            <span class="bi bi-tools fs-4 me-1"></span>Interfaces Settings
                        </a>
                    </li>
                    @endif
                </ul>
                <hr>
                <div class="small d-flex justify-content-around flex-wrap">
                    <a class="nav-link text-reset" href="https://github.com/sandyh90/rockstream-streaming-relay"
                        target="_blank"><span class="bi bi-github me-1"></span>Github Source</a>
                    <a class="nav-link text-reset" href="https://trakteer.id/pickedianz/tip" target="_blank"><span
                            class="bi bi-heart-fill text-danger me-1"></span>Support Me On Trakteer</a>
                </div>
            </div>
        </div>

        <div class="p-3">
            @yield('content')
        </div>
    </div>

    <footer class="bg-light p-4 border-top">
        <div class="container-fluid text-muted">
            <div class="d-flex justify-content-between flex-wrap">
                <div class="small">
                    Copyright &copy; {{ date('Y') }}<a href="{{ url('/') }}" class="ms-1">RockStream V1.0.2</a>
                    <div class="d-inline">Powered By<a href="https://github.com/sandyh90" target="_blank"
                            class="ms-1">Pickedianz</a>
                    </div>
                </div>
                <div class="fw-light">
                    Page rendered in <strong>{{ round(microtime(true) - LARAVEL_START, 4) }}</strong> seconds.
                </div>
            </div>
    </footer>

    <!-- Bootstrap Bundle with Popper Important -->
    <script src="{{ asset('assets/vendor/bootstrap-5.0.2/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Addons Javascript Module -->
    <script src="{{ asset('assets/vendor/clipboard.js-2.0.8/clipboard.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/dataTables.min.js') }}"></script>

    <!-- Custom Javascript Module -->
    <script src="{{ asset('assets/js/custom.js') }}"></script>

    @yield('js-content')

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            get_rtmp_stat()

            if (!window.localStorage.about_modal_once) {
                $('.modal-about-display').modal('show');
                $('.btn-dismiss-about-modal').click(function () {
                    window.localStorage.about_modal_once = true;
                });
            }

        });
    </script>
    <script>
        function get_rtmp_stat() {
            $.ajax({
                type: "GET",
                url: "{{ route('panel.data_rtmp_stat') }}",
                async: true,
                success: function (data) {
                    if(data.success == true){
                        $(".indicator-rtmp .uptime").html(data.xml.uptime);
                        $(".indicator-rtmp .bytes-in-out").html(data.xml.bytes_in_out);
                        $(".indicator-rtmp .bandwidth").html(data.xml.bandwidth);
                        if(data.xml.status == true){
                            $(".indicator-rtmp .status-stream").html('<span class="bi bi-cloud-check me-1"></span>Online').removeClass('bg-danger').addClass('bg-success');
                        }else{
                            $(".indicator-rtmp .status-stream").html('<span class="bi bi-cloud-slash me-1"></span>Offline').removeClass('bg-success').addClass('bg-danger');
                        }
                        setTimeout('get_rtmp_stat()', 2000);
                    }
                },
                error: function (data) {
                    $(".indicator-rtmp .uptime").html('NaN');
                    $(".indicator-rtmp .bytes-in-out").html('NaN');
                    $(".indicator-rtmp .bandwidth").html('NaN');
                    $(".indicator-rtmp .status-stream").html('<span class="bi bi-cloud-minus me-1"></span>NaN').removeClass('bg-success bg-danger').addClass('bg-secondary');
                    setTimeout('get_rtmp_stat()', 2000);
                }
            });
            
        }
    </script>
</body>

<div class="modal fade custom-modal-display" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="custom-modal-content">
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-about-display bg-secondary" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content rounded-6 shadow">
            <div class="modal-body p-5">
                @include('layouts.info_layouts.about_modal')
                <button type="button" class="btn btn-lg btn-primary btn-dismiss-about-modal mt-5 w-100"
                    data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

@yield('modal-content')

</html>