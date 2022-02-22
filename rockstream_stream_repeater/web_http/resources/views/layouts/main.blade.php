<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title') - {{ env('APP_NAME','RockStream') }}</title>

    @yield('head-content')

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="{{ asset('assets/vendor/bootstrap-5.0.2/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/google-icon/google-icon.css') }}" rel="stylesheet">
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
                <a class="navbar-brand" href="{{ route('home') }}">{{ env('APP_NAME','RockStream') }}</a>
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
                                        class="bi bi-123 me-1"></span>Bytes:
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
                                Hello,<span class="ms-1">{{ \Str::of(Auth::user()->name)->limit(13); }}</span>
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

        <div class="offcanvas offcanvas-start sidebar-toggle" tabindex="-1" data-bs-backdrop="true">
            <div class="offcanvas-body">
                <div class="h4 d-inline">{{ env('APP_NAME','RockStream') }}</div>
                <button type="button" class="btn btn-outline" data-bs-dismiss="offcanvas"><span
                        class="material-icons">menu</span></button>
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
                        <a href="{{ route('user.settings') }}" class="nav-link text-reset">
                            <span class="bi bi-person-circle fs-4 me-1"></span>Account Settings
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('premiere.home') }}" class="nav-link text-reset">
                            <span class="bi bi-play-btn fs-4 me-1"></span>Premiere Video
                        </a>
                    </li>
                </ul>
                <hr>
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
                    Copyright &copy; {{ date('Y') }}<a href="{{ url('/') }}" class="ms-1">{{
                        env('APP_NAME','RockStream') }}</a>
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
                <div class="text-center mb-0">
                    <h2 class="fw-bold">About</h2>
                    <h5 class="fw-light">{{ env('APP_NAME','RockStream') }}</h5>
                </div>
                <ul class="d-grid gap-4 my-5 list-unstyled">
                    <li class="d-flex gap-4">
                        <span class="bi bi-broadcast fs-1 text-primary flex-shrink-0"></span>
                        <div>
                            <h5 class="mb-0">Stream To Multi Endpoint</h5>
                            Stream to multiple endpoints with one input stream relay.
                        </div>
                    </li>
                    <li class="d-flex gap-4">
                        <span class="bi bi-emoji-laughing fs-1 text-success flex-shrink-0"></span>
                        <div>
                            <h5 class="mb-0">Easy To Use</h5>
                            We want create this application Easy to use and easy to understand with other.
                        </div>
                    </li>
                    <li class="d-flex gap-4">
                        <span class="bi bi-collection-play fs-1 text-danger flex-shrink-0"></span>
                        <div>
                            <h5 class="mb-0">Watch On Other Platform</h5>
                            You can watch stream on other platform that come from this application.
                        </div>
                    </li>
                    <li class="d-flex gap-4">
                        <span class="bi bi-usb-drive fs-1 text-secondary flex-shrink-0"></span>
                        <div>
                            <h5 class="mb-0">Portable Application</h5>
                            You can use this software on any device without installation.
                        </div>
                    </li>
                    <li class="d-flex gap-4">
                        <span class="bi bi-windows fs-1 text-info flex-shrink-0"></span>
                        <div>
                            <h5 class="mb-0">Design For Windows</h5>
                            This app design only for windows and maybe not working properly if use outside windows
                            platform.
                        </div>
                    </li>
                </ul>
                <hr>
                <div class="fw-light fs-5">Recommended:</div>
                <ul>
                    <li><span class="bi bi-ethernet fs-4 me-1"></span>High Speed Internet Min. 10 Mbps and upload speed
                        Min. 5 Mbps or High.</li>
                    <li><span class="bi bi-cpu fs-4 me-1"></span>Processor Min. Dual Core with speed clock 2.40 GHz or
                        High
                        (Except: Premiere Video Transcoding).</li>
                    <li><span class="bi bi-device-hdd fs-4 me-1"></span>Storage free space capacity Min. 4 GB.</li>
                    <li><span class="bi bi-gpu-card fs-4 me-1"></span>(Optional: For Premiere Video Transcoding) Use
                        external / dedicated graphics card.</li>
                </ul>
                <div class="fw-light fs-5">Limited Experience:</div>
                <ul>
                    <li><span class="bi bi-plus-slash-minus fs-4 me-1"></span>Due nginx service built for windows
                        version and
                        some feature may not work like in linux version.
                    </li>
                    <li><span class="bi bi-question-square fs-4 me-1"></span>Status RTMP in navbar reseting to
                        beginning if nginx reload because of nginx process use old
                        process and not automatically use new process, this need <strong>per_worker</strong> listener
                        for now only support in linux version. <a href="https://github.com/arut/nginx-patches"
                            target="_blank">[Nginx
                            Patches Source]</a></li>
                    <li><span class="bi bi-shield-slash fs-4 me-1"></span>RTMPS protocol for now not supported on
                        this application, and will supported soon on new
                        version.</li>
                    <li><span class="bi bi-tools fs-4 me-1"></span>There are still bug remaining on app, Please report
                        any bugs you find.</li>
                </ul>
                <button type="button" class="btn btn-lg btn-primary btn-dismiss-about-modal mt-5 w-100"
                    data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

@yield('modal-content')

</html>