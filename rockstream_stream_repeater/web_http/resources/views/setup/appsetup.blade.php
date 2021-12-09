<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Setup App</title>

    @yield('head-content')

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="{{ asset('assets/vendor/bootstrap-5.0.2/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/google-icon/google-icon.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
</head>

<body class="d-flex flex-column min-vh-100 justify-content-center align-items-center">
    <h1>{{ env('APP_NAME','RockStream') }}</h1>
    <div class="col-xl-5">
        <div class="card shadow-lg">
            <div class="card-body">
                <h4 class="text-center fw-lighter">Welcome</h4>
                {!! Session::get('setup_msg') !!}
                <div class="float-end" data-bs-toggle="collapse" data-bs-target=".info-setup-app" aria-expanded="false">
                    <span class="bi bi-question-circle"></span>
                </div>
                <div class=" collapse collapse-horizontal info-setup-app">
                    <div class="small text-muted text-center my-2">You can use setup account app from console with using
                        artisan command <strong>"php artisan db:seed"</strong>
                    </div>
                </div>
                <form method="POST" action="{{ route('setup.process') }}">
                    @csrf
                    <div class="fs-5 fw-light">
                        Account App
                    </div>
                    <hr>
                    <div class="form-group p-2">
                        <label class="form-label">Fullname Account</label>
                        <div class="input-group">
                            <div class="input-group-text"><span class="material-icons">badge</span></div>
                            <input id="fullname" type="text"
                                class="form-control @error('fullname') is-invalid @enderror" name="fullname"
                                value="{{ old('fullname') }}" autofocus>
                            @error('fullname')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group p-2">
                        <label class="form-label">Username</label>
                        <div class="input-group">
                            <div class="input-group-text"><span class="material-icons">account_circle</span></div>
                            <input id="username" type="text"
                                class="form-control @error('username') is-invalid @enderror" name="username"
                                value="{{ old('username') }}" autofocus>
                            @error('username')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row g-3 p-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Password</label>
                            <div class="input-group password-toggle">
                                <div class="input-group-text"><span class="material-icons">lock</span></div>
                                <input id="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password">
                                @error('password')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">Password Confirm</label>
                            <div class="input-group password-toggle">
                                <input id="password-confirm" type="password"
                                    class="form-control @error('password_confirm') is-invalid @enderror"
                                    name="password_confirm">
                                <button type="button" class="input-group-text btn-toggle-password"
                                    data-bs-toggle="tooltip" data-bs-original-title="Show Password"><span
                                        class="material-icons">visibility_off</span></button>
                                @error('password_confirm')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group p-2">
                        <button type="submit" class="btn btn-primary">
                            <span class="material-icons me-1">build</span>Finish Setup
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center small">
                <div class="container">
                    Copyright &copy; {{ date('Y') }}<a href="{{ url('/') }}" class="ms-1">{{
                        env('APP_NAME','RockStream') }}</a>
                    <div class="d-inline">Powered By<a href="https://github.com/sandyh90" target="_blank"
                            class="ms-1">Pickedianz</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper Important -->
    <script src="{{ asset('assets/vendor/bootstrap-5.0.2/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Addons Javascript Module -->
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            $(".password-toggle button.btn-toggle-password").on("click", function(event) {
                event.preventDefault();
                if ($(".password-toggle input").attr("type") == "text") {
                    $(".password-toggle input").attr("type", "password");
                    $(".password-toggle button.btn-toggle-password span").html("visibility_off");
                } else if ($(".password-toggle input").attr("type") == "password") {
                    $(".password-toggle input").attr("type", "text");
                    $(".password-toggle button.btn-toggle-password span").html("visibility");
                }
            });
        });
    </script>
</body>

</html>