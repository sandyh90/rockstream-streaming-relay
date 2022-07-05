<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Setup App - RockStream</title>

    @yield('head-content')

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="{{ asset('assets/vendor/bootstrap-5.2.0/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    <link href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">

    <!-- Addons Javascript Module [First Start] -->
    <script defer src="{{ asset('assets/vendor/alpine.js/cdn.min.js') }}"></script>
</head>

<body class="d-flex flex-column min-vh-100 justify-content-center align-items-center bg-auth-custom-gradient">
    <div class="col-xl-5">
        <div class="card shadow-lg">
            <div class="card-body">
                <img src="{{ asset('assets/img/rockstream-brand.png') }}" height="100%" width="100%">
                <hr>
                <h4 class="text-center fw-lighter">Welcome</h4>
                {!! Session::get('setup_msg') !!}
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
                <form method="POST" action="{{ route('setup.process') }}">
                    @csrf
                    <ul class="nav nav-tabs" id="nav-app-setup-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="nav-account-setup-tab" data-bs-toggle="tab"
                                data-bs-target="#nav-account-setup" type="button" role="tab"
                                aria-controls="nav-account-setup" aria-selected="true"><span
                                    class="bi bi-person-badge me-1"></span>Account Setup</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="nav-preferences-setup-tab" data-bs-toggle="tab"
                                data-bs-target="#nav-preferences-setup" type="button" role="tab"
                                aria-controls="nav-preferences-setup" aria-selected="false"><span
                                    class="bi bi-gear me-1"></span>Preferences Setup</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-account-setup" role="tabpanel"
                            aria-labelledby="nav-account-setup-tab">
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
                                        value="{{ old('username') }}">
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
                            <div class="p-2">
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-primary next-step-tab" type="button"><span
                                            class="bi bi-arrow-right me-1"></span>Next</button>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-preferences-setup" role="tabpanel"
                            aria-labelledby="nav-preferences-setup-tab">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <span class="bi bi-display fs-3"></span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="form-group p-2">
                                        <div class="form-check form-switch">
                                            <input
                                                class="form-check-input @error('use_live_preview') is-invalid @enderror"
                                                type="checkbox" role="switch" id="use-live-preview-feature"
                                                name="use_live_preview" value="1" {{ old('use_live_preview')==TRUE
                                                ? 'checked' : NULL}}>
                                            <label class="form-check-label" for="use-live-preview-feature">Use Live
                                                Preview</label>
                                        </div>
                                        @error('use_live_preview')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <span class="bi bi-info-circle fs-3"></span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="form-group p-2">
                                        <div class="form-check form-switch">
                                            <input
                                                class="form-check-input @error('disable_auto_show_about') is-invalid @enderror"
                                                type="checkbox" role="switch" id="disable-auto-show-about"
                                                name="disable_auto_show_about" value="1" {{
                                                old('disable_auto_show_about')==TRUE ? 'checked' : NULL}}>
                                            <label class="form-check-label" for="disable-auto-show-about">
                                                Disable Auto Show About</label>
                                        </div>
                                        @error('disable_auto_show_about')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                        <div class="small">
                                            <span class="text-danger me-1">Note:</span>This will disable the auto show
                                            about modal when using fresh web browser.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="align-middle">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="28px" height="28px"
                                                version="1.1" viewBox="0 0 512 512">
                                                <path
                                                    d="M103.462,207.061c15.821,0,26.365,2.92,31.637,8.761c5.267,5.841,6.522,15.867,3.766,30.074    c-2.879,14.795-8.423,25.355-16.641,31.682c-8.218,6.328-20.724,9.488-37.513,9.488h-25.33l15.549-80.005H103.462z M2,368.057    h41.643l9.877-50.823h35.669c15.739,0,28.686-1.65,38.85-4.96c10.165-3.305,19.402-8.848,27.717-16.63    c6.978-6.415,12.624-13.49,16.948-21.227c4.319-7.731,7.388-16.267,9.202-25.601c4.406-22.66,1.081-40.31-9.965-52.955    s-28.619-18.967-52.709-18.967H39.154L2,368.057z"
                                                    fill="#000003" />
                                                <path
                                                    d="M212.49,126.071h41.314l-9.878,50.823h36.806c23.157,0,39.132,4.042,47.924,12.117    c8.791,8.08,11.425,21.17,7.91,39.266l-17.286,88.957H277.31l16.436-84.582c1.87-9.622,1.184-16.185-2.064-19.684    c-3.248-3.5-10.159-5.251-20.729-5.251h-33.02L216.65,317.233h-41.315L212.49,126.071z"
                                                    fill="#000003" />
                                                <path
                                                    d="M428.49,207.061c15.821,0,26.365,2.92,31.637,8.761c5.269,5.841,6.523,15.867,3.766,30.074    c-2.879,14.795-8.421,25.355-16.641,31.682c-8.218,6.328-20.724,9.488-37.513,9.488H384.41l15.549-80.005H428.49z     M327.029,368.057h41.643l9.876-50.823h35.669c15.739,0,28.686-1.65,38.851-4.96c10.164-3.305,19.401-8.848,27.717-16.63    c6.979-6.415,12.624-13.49,16.948-21.227c4.318-7.731,7.388-16.267,9.201-25.601c4.406-22.66,1.082-40.31-9.965-52.955    c-11.046-12.645-28.619-18.967-52.709-18.967h-80.076L327.029,368.057z"
                                                    fill="#000003" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="input-group p-2">
                                            <div class="input-group-text">
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input @error('enable_custom_php_path') is-invalid @enderror"
                                                        type="checkbox" id="enable-custom-php-path"
                                                        name="enable_custom_php_path" value="1" {{
                                                        old('enable_custom_php_path')==TRUE ? 'checked' : NULL}}>
                                                    <label class="form-check-label"
                                                        for="enable-custom-php-path">Enable</label>
                                                </div>
                                            </div>
                                            <input type="text"
                                                class="form-control @error('php_custom_dir') is-invalid @enderror"
                                                name="php_custom_dir" placeholder="PHP Binary Custom Folder Path"
                                                value="{{ old('php_custom_dir') }}">
                                            @error('enable_custom_php_path')
                                            <span class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                            @error('php_custom_dir')
                                            <span class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="align-middle">
                                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="28px"
                                                height="28px" viewBox="0 0 512 512">
                                                <polygon
                                                    points="201.4592743,35.1842918 93.1139832,161.5448151 93.1139832,215.0151215 254.0410767,30.7333698 512,8.9018469 133.8661652,406.6586609 183.7402039,409.5357056 458.3604431,126.5212936 458.3604431,395.1674805 427.6439819,424.0307922 505.6705322,428.6425171 505.6705322,503.0981445 262.0882874,482.4682007 385.8341064,362.0732727 385.8341064,302.3327332 209.1849213,478.0003662 0,460.2897644 336.7639465,93.8755493 280.8904724,97.1756668 37.6974564,366.9811401 37.6974564,137.741684 60.5782471,110.1984177 4.3239956,113.5239258 4.3239956,51.8710251 " />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="input-group p-2">
                                            <div class="input-group-text">
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input @error('enable_custom_ffmpeg_path') is-invalid @enderror"
                                                        type="checkbox" id="enable-custom-ffmpeg-path"
                                                        name="enable_custom_ffmpeg_path" value="1" {{
                                                        old('enable_custom_ffmpeg_path')==TRUE ? 'checked' : NULL}}>
                                                    <label class="form-check-label"
                                                        for="enable-custom-ffmpeg-path">Enable</label>
                                                </div>
                                            </div>
                                            <input type="text"
                                                class="form-control @error('ffmpeg_custom_dir') is-invalid @enderror"
                                                name="ffmpeg_custom_dir" placeholder="FFmpeg Binary Custom Folder Path"
                                                value="{{ old('ffmpeg_custom_dir') }}">
                                            @error('enable_custom_ffmpeg_path')
                                            <span class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                            @error('ffmpeg_custom_dir')
                                            <span class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="align-middle">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="28px" height="28px"
                                                version="1.1" viewBox="0 0 32 32">
                                                <path
                                                    d="M16,0L2.1,8v16L16,32l13.9-8V8L16,0z M24,22.1c0,0.9-0.9,1.7-2,1.7c-0.8,0-1.8-0.3-2.4-1.1l-8-9.5v8.9c0,1-0.8,1.7-1.7,1.7  H9.8c-1,0-1.7-0.8-1.7-1.7V9.9c0-0.9,0.8-1.7,2-1.7c0.9,0,1.8,0.3,2.4,1.1l8,9.5V9.9c0-1,0.8-1.7,1.7-1.7h0.1c1,0,1.7,0.8,1.7,1.7  L24,22.1L24,22.1z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="input-group p-2">
                                            <div class="input-group-text">
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input @error('enable_custom_nginx_path') is-invalid @enderror"
                                                        type="checkbox" id="enable-custom-nginx-path"
                                                        name="enable_custom_nginx_path" value="1" {{
                                                        old('enable_custom_nginx_path')==TRUE ? 'checked' : NULL}}>
                                                    <label class="form-check-label"
                                                        for="enable-custom-nginx-path">Enable</label>
                                                </div>
                                            </div>
                                            <input type="text"
                                                class="form-control @error('nginx_custom_dir') is-invalid @enderror"
                                                name="nginx_custom_dir" placeholder="Nginx Binary Custom Folder Path"
                                                value="{{ old('nginx_custom_dir') }}">
                                            @error('enable_custom_nginx_path')
                                            <span class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                            @error('nginx_custom_dir')
                                            <span class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                        <div class="small">
                                            <span class="text-danger me-1">Note:</span>Your nginx binary must be
                                            included rtmp module.
                                        </div>
                                    </div>
                                </div>
                                <div class="small text-muted">*For all path binary please remove quote (") and only
                                    path to directory folder without pointing to file if present.</div>
                            </div>
                            <div class="p-2">
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-primary previous-step-tab" type="button"><span
                                            class="bi bi-arrow-left me-1"></span>Back</button>
                                    <button type="submit" class="btn btn-primary">
                                        <span class="bi bi-tools me-1"></span>Finish Setup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
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
    <script src="{{ asset('assets/vendor/bootstrap-5.2.0/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Addons Javascript Module -->
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            $('input[name="php_custom_dir"], input[name="ffmpeg_custom_dir"], input[name="nginx_custom_dir"]').on('keyup keypress change', function(e) {
                var value = $(this).val();
                if (value.indexOf('"') != -1) {
                    $(this).val(value.replace(/\"/g, ""));
                }else if (value.indexOf('\'') != -1) {
                    $(this).val(value.replace(/\'/g, ""));
                }
            });

            $('.next-step-tab').click(function() {
            const nextTabLinkEl = $('.nav-tabs .active').closest('li').next('li').find('button')[0];
            const nextTab = new bootstrap.Tab(nextTabLinkEl);
            nextTab.show();
            });

            $('.previous-step-tab').click(function() {
            const prevTabLinkEl = $('.nav-tabs .active').closest('li').prev('li').find('button')[0];
            const prevTab = new bootstrap.Tab(prevTabLinkEl);
            prevTab.show();
            });

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