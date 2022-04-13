<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Setup App - {{ env('APP_NAME','RockStream') }}</title>

    @yield('head-content')

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="{{ asset('assets/vendor/bootstrap-5.0.2/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/google-icon/google-icon.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    <link href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">

    <!-- Addons Javascript Module [First Start] -->
    <script defer src="{{ asset('assets/vendor/alpine.js/cdn.min.js') }}"></script>
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
                        Setup App Options
                    </div>
                    <hr>
                    <div class="setup-app-options p-2">
                        <button class="btn btn-primary setup-generate-appkey">
                            <span class="bi bi-key me-1"></span>Generate App Key
                        </button>
                    </div>
                    <div class="small text-muted">*After you click button "Generate App Key" the page will be
                        reload automatically.</div>
                    <div class="fs-5 fw-light">
                        Account App
                    </div>
                    <hr>
                    <div class="form-group p-2">
                        <label class="form-label">Fullname Account</label>
                        <div class="input-group">
                            <div class="input-group-text"><span class="bi bi-person-lines-fill"></span></div>
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
                            <div class="input-group-text"><span class="bi bi-person-circle"></span></div>
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
                    <div class="form-group row g-3 p-2" x-data="{ input: 'password' }">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <div class="input-group-text"><span class="bi bi-lock"></span></div>
                                <input id="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password"
                                    x-bind:type="input">
                                @error('password')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">Password Confirm</label>
                            <div class="input-group">
                                <input id="password-confirm" type="password"
                                    class="form-control @error('password_confirm') is-invalid @enderror"
                                    name="password_confirm" x-bind:type="input">
                                <button type="button" class="input-group-text" data-bs-toggle="tooltip"
                                    data-bs-original-title="Show Stream Key"
                                    x-on:click="input = (input === 'password') ? 'text' : 'password'"><span
                                        :class="{'bi bi-eye-slash' : input != 'password','bi bi-eye': input != 'text'}"></span></button>
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
                            <span class="bi bi-tools me-1"></span>Finish Setup
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
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            $(".setup-app-options").on('click', '.setup-generate-appkey', function(event) {
                $.ajax({
                    url: "{{ route('setup.generate_appkey') }}",
                    type: 'OPTIONS',
                    async: true,
                    beforeSend: function() {
                        $(".setup-generate-appkey").on('.setup-app-options').html("<span class='spinner-border spinner-border-sm me-1'></span>Generating App Key").attr("disabled", true);
                        swal.fire({
                            title: "Generating App Key",
                            text: "Please wait",
                            showConfirmButton: false,
                            allowOutsideClick: false
                        });
                        Swal.showLoading();
                    },
                    success: function(data) {
                        $(".setup-generate-appkey").on('.setup-app-options').html("<span class='bi bi-key me-1'></span>Generate App Key").attr("disabled", false);
                        swal.fire({
                            icon: data.alert.icon,
                            title: data.alert.title,
                            text: data.alert.text,
                            showConfirmButton: false,
                            timer: 1500,
                            timerProgressBar: true
                        });
                        $('input[name=_token]').val(data.csrftoken);
                        $('meta[name="csrf-token"]').val(data.csrftoken);
                        location.reload();
                    },
                    error: function(err) {
                        $(".setup-generate-appkey").on('.setup-app-options').html("<span class='bi bi-key me-1'></span>Generate App Key").attr("disabled", false);
                        swal.fire("Generating App Key Failed", "There have problem while generating app key!", "error");
                    }
                });
                event.preventDefault();
            });
        });
    </script>
</body>

</html>