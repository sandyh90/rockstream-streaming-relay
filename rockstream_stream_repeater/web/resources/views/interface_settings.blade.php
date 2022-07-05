@extends('layouts.main')

@section('title','Interface Settings')

@section('head-content')
<!-- Seperate Addons CSS -->
<link rel="stylesheet" href="{{ asset('assets/vendor/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/select2/css/select2-bootstrap4.min.css') }}">
@endsection

@section('content')
<div class="px-4 py-5 my-5">
    <h1 class="display-5 fw-bold text-center"><span class="bi bi-tools me-3"></span>Interface Settings
    </h1>
    <div class="col-xl-5 col-lg-6 mx-auto">
        <div class="card">
            <div class="card-body">
                {!! Session::get('itf_setting_msg') !!}
                <nav>
                    <div class="nav nav-tabs" id="nav-interfaces-settings-tab" role="tablist">
                        <button class="nav-link active" id="nav-app-settings-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-app-settings" type="button" role="tab" aria-controls="nav-app-settings"
                            aria-selected="true"><span class="bi bi-sliders me-1"></span>App Settings</button>
                        <button class="nav-link" id="nav-misc-tab" data-bs-toggle="tab" data-bs-target="#nav-misc"
                            type="button" role="tab" aria-controls="nav-misc"><span
                                class="bi bi-gear me-1"></span>Misc</button>
                    </div>
                </nav>
                <div class="tab-content mt-2" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-app-settings" role="tabpanel"
                        aria-labelledby="nav-app-settings-tab">
                        <div class="fw-light fs-5">App Settings</div>
                        <hr>
                        <form class="form-app-settings">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <span class="bi bi-display fs-3"></span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="form-group p-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="use-live-preview-feature" name="use_live_preview" value="1" {{
                                                (!array_key_exists('USE_LIVE_PREVIEW',$getSettingConfig)? ''
                                                :($getSettingConfig['USE_LIVE_PREVIEW']==TRUE ?'checked':'')) }}>
                                            <label class="form-check-label" for="use-live-preview-feature">Use Live
                                                Preview</label>
                                        </div>
                                        <div class="small">
                                            <span class="text-danger me-1">Note:</span>You need switch on "Save with
                                            Reload System" if you want save with auto reload system.
                                        </div>
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
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="disable-auto-show-about" name="disable_auto_show_about" value="1" {{
                                                (!array_key_exists('DISABLE_AUTO_SHOW_ABOUT',$getSettingConfig)? ''
                                                :($getSettingConfig['DISABLE_AUTO_SHOW_ABOUT']==TRUE ?'checked':'')) }}>
                                            <label class="form-check-label" for="disable-auto-show-about">
                                                Disable Auto Show About</label>
                                        </div>
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
                                        <div class="p-2">
                                            <label class="form-label">PHP Custom Binary</label>
                                            <div class="input-group">
                                                <div class="input-group-text">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="enable-custom-php-path" name="enable_custom_php_path"
                                                            value="1" {{
                                                            (!array_key_exists('IS_CUSTOM_PHP_BINARY',$getSettingConfig)? ''
                                                            :($getSettingConfig['IS_CUSTOM_PHP_BINARY']==TRUE
                                                            ?'checked':'')) }}>
                                                        <label class="form-check-label"
                                                            for="enable-custom-php-path">Enable</label>
                                                    </div>
                                                </div>
                                                <input type="text" class="form-control" name="php_custom_dir"
                                                    placeholder="PHP Binary Custom Folder Path" value="{{
                                                (!array_key_exists('PHP_BINARY_DIRECTORY' , $getSettingConfig) ? ''
                                                : (!empty($getSettingConfig['PHP_BINARY_DIRECTORY']) ? $getSettingConfig['PHP_BINARY_DIRECTORY'] : ''))
                                                }}">
                                            </div>
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
                                        <div class="p-2">
                                            <label class="form-label">FFmpeg Custom Binary</label>
                                            <div class="input-group">
                                                <div class="input-group-text">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="enable-custom-ffmpeg-path"
                                                            name="enable_custom_ffmpeg_path" value="1" {{
                                                            (!array_key_exists('IS_CUSTOM_FFMPEG_BINARY',$getSettingConfig)? ''
                                                            :($getSettingConfig['IS_CUSTOM_FFMPEG_BINARY']==TRUE
                                                            ?'checked':'')) }}>
                                                        <label class="form-check-label"
                                                            for="enable-custom-ffmpeg-path">Enable</label>
                                                    </div>
                                                </div>
                                                <input type="text" class="form-control" name="ffmpeg_custom_dir"
                                                    placeholder="FFmpeg Binary Custom Folder Path" value="{{
                                                (!array_key_exists('FFMPEG_BINARY_DIRECTORY' , $getSettingConfig) ? ''
                                                : (!empty($getSettingConfig['FFMPEG_BINARY_DIRECTORY']) ? $getSettingConfig['FFMPEG_BINARY_DIRECTORY'] : ''))
                                                }}">
                                            </div>
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
                                        <div class="p-2">
                                            <label class="form-label">Nginx Custom Binary</label>
                                            <div class="input-group">
                                                <div class="input-group-text">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="enable-custom-nginx-path"
                                                            name="enable_custom_nginx_path" value="1" {{
                                                            (!array_key_exists('IS_CUSTOM_NGINX_BINARY',$getSettingConfig)? ''
                                                            :($getSettingConfig['IS_CUSTOM_NGINX_BINARY']==TRUE
                                                            ?'checked':'')) }}>
                                                        <label class="form-check-label"
                                                            for="enable-custom-nginx-path">Enable</label>
                                                    </div>
                                                </div>
                                                <input type="text" class="form-control" name="nginx_custom_dir"
                                                    placeholder="Nginx Binary Custom Folder Path" value="{{
                                                (!array_key_exists('NGINX_BINARY_DIRECTORY' , $getSettingConfig) ? ''
                                                : (!empty($getSettingConfig['NGINX_BINARY_DIRECTORY']) ? $getSettingConfig['NGINX_BINARY_DIRECTORY'] : ''))
                                                }}">
                                            </div>
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
                            <div class="my-2 app-settings-info-data"></div>
                            <div class="d-flex justify-content-between flex-wrap">
                                <div class="form-group p-2">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="reload-system-switch" name="reload_system_switch" value="1">
                                        <label class="form-check-label" for="reload-system-switch">Save With Reload
                                            System</label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-app-settings-save"><span
                                        class="bi bi-save me-1"></span>Save</button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="nav-misc" role="tabpanel" aria-labelledby="nav-misc-tab">
                        <div class="fw-light fs-5">Miscellaneous</div>
                        <hr>
                        <div class="misc-section-settings">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <span class="bi bi-collection-play fs-3"></span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="p-2">
                                        <div class="fw-light">
                                            Launch Test Stream
                                        </div>
                                        <div class="btn-group test-streaming-btn-group">
                                            <div class="btn btn-primary launch-test-stream">
                                                <span class="bi bi-film me-1"></span>Test Stream
                                            </div>
                                            <div class="btn btn-info launch-daemon-test-stream">
                                                <span class="bi bi-cpu me-1"></span>Launch Daemon
                                            </div>
                                        </div>
                                        <div class="small text-muted">*Daemon will automatically stop if there no queue
                                            test
                                            stream.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <span class="bi bi-arrow-counterclockwise fs-3"></span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="p-2">
                                        <div class="fw-light">
                                            Reset To Factory
                                        </div>
                                        <div class="btn btn-danger reset-to-factory">
                                            <span class="bi bi-arrow-counterclockwise me-1"></span>Reset
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-content')
<!-- Seperate Addons Javascript-->
<script src="{{ asset('assets/vendor/select2/js/select2.full.min.js') }}"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        $('input[name="php_custom_dir"], input[name="ffmpeg_custom_dir"], input[name="nginx_custom_dir"]').on('keyup keypress change', function(e) {
            var value = $(this).val();
            if (value.indexOf('"') != -1) {
                $(this).val(value.replace(/\"/g, ""));
            }else if (value.indexOf('\'') != -1) {
                $(this).val(value.replace(/\'/g, ""));
            }
        });

        $('.form-app-settings').on('submit', function(event) {
            event.preventDefault();
            var form = this;
            var formdata = new FormData(this);
            $.ajax({
                type: "POST",
                url: "{{ route('interfaces.edit_app_settings') }}",
                data: formdata,
                processData: false,
                contentType: false,
                async: true,
                beforeSend: function() {
                    $(".btn-app-settings-save").html("<span class='spinner-border spinner-border-sm fa-spin me-1'></span>Saving").attr("disabled", true);
                },
                success: function(data) {
                    if (data.success == false) {
                        if(data.isForm == true){
                            msgalert(".app-settings-info-data", data.messages);
                        }else{
                            if ($(".app-settings-info-data").hasClass("alert alert-danger")) {
                                $(".app-settings-info-data").removeClass("alert alert-danger");
                            }
                            $(".app-settings-info-data").html(data.messages).show();
                        }
                    } else {
                        if ($(".app-settings-info-data").hasClass("alert alert-danger")) {
                            $(".app-settings-info-data").removeClass("alert alert-danger");
                        }
                        $(".app-settings-info-data").html(data.messages).show().delay(3000).fadeOut();
                        $('.add-input-stream').modal('hide');
                        location.reload();
                    }
                    $(".btn-app-settings-save").html("<span class='bi bi-save me-1'></span>Save").attr("disabled", false);
                    $('meta[name="csrf-token"]').val(data.csrftoken);
                },
                error: function() {
                    $(".btn-app-settings-save").html("<span class='bi bi-save me-1'></span>Save").attr("disabled", false);
                    swal.fire("Edit App Settings Error", "There have problem while editing app settings!", "error");
                }
            });
        });

        $(".misc-section-settings").on('click', '.reset-to-factory', function(event) {
            Swal.fire({
                title: 'Reset To Factory',
                text: "Are you sure you want to reset to factory?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Reset'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('interfaces.reset_factory') }}",
                        type: 'POST',
                        async: true,
                        beforeSend: function() {
                            swal.fire({
                                title: "Resetting...",
                                text: "Please wait",
                                showConfirmButton: false,
                                allowOutsideClick: false
                            });
                            Swal.showLoading();
                        },
                        success: function(data) {
                            swal.fire({
                                icon: data.alert.icon,
                                title: data.alert.title,
                                showConfirmButton: false,
                                timer: 1500,
                                timerProgressBar: true
                            });
                            $('meta[name="csrf-token"]').val(data.csrftoken);
                            location.reload();
                        },
                        error: function(err) {
                            swal.fire("Reset To Factory Failed", "There have problem while reset to factory!", "error");
                        }
                    });
                }
            });
            event.preventDefault();
        });

        $(".misc-section-settings").on('click', '.launch-test-stream', function(event) {
            $.ajax({
                url: "{{ route('interfaces.start_teststream',['fetch' => 'show']) }}",
                type: 'POST',
                async: true,
                beforeSend: function() {
                    $('.custom-modal-display').modal('show');
                    $('.custom-modal-content').html("<span class='spinner-border my-2'></span>").addClass("text-center");
                },
                success: function(data) {
                    $('meta[name="csrf-token"]').val(data.csrftoken);
                    $('input[name=_token]').val(data.csrftoken);
                    if(data.success == true){
                        $('.custom-modal-content').html(data.html).removeClass("text-center");
                        $('.form-start-test-streaming').submit(function(e) {
                            e.preventDefault();
                            var form = this;
                            var formdata = new FormData(form);
                            $.ajax({
                                url: "{{ route('interfaces.start_teststream',['fetch' => 'start']) }}",
                                type: 'POST',
                                data: formdata,
                                processData: false,
                                contentType: false,
                                async: true,
                                beforeSend: function() {
                                    $(".btn-start-test-streaming").on('.custom-modal-content').html("<span class='spinner-border spinner-border-sm me-1'></span>Starting Test Stream").attr("disabled", true);
                                },
                                success: function(data) {
                                    $('meta[name="csrf-token"]').val(data.csrftoken);
                                    $('input[name=_token]').val(data.csrftoken);
                                    if (data.success == false) {
                                        if(data.isForm == true){
                                            msgalert(".start-test-streaming-info-data", data.messages);
                                        }else{
                                            if ($(".start-test-streaming-info-data").hasClass("alert alert-danger")) {
                                                $(".start-test-streaming-info-data").removeClass("alert alert-danger");
                                            }
                                            $(".start-test-streaming-info-data").html(data.messages).show();
                                        }
                                    } else {
                                        $('.premiere-video-data').DataTable().ajax.reload();
                                        if ($(".start-test-streaming-info-data").hasClass("alert alert-danger")) {
                                            $(".start-test-streaming-info-data").removeClass("alert alert-danger");
                                        }
                                        $(".start-test-streaming-info-data").html(data.messages).show().delay(3000).fadeOut();
                                        $('.custom-modal-display').modal('hide');
                                        form.reset();
                                    }
                                    $(".btn-start-test-streaming").on('.custom-modal-content').html("<span class='bi bi-broadcast me-1'></span>Start Test").attr("disabled", false);
                                },
                                error: function() {
                                    $(".btn-start-test-streaming").on('.custom-modal-content').html("<span class='bi bi-broadcast me-1'></span>Start Test").attr("disabled", false);
                                }
                            });
                        });
                    }else{
                        $('.custom-modal-content').html(data.messages);
                    }
                },
                error: function(err) {
                    $('.custom-modal-content').html("<span class='bi bi-exclamation-triangle me-1'></span>There have problem while processing data").addClass("text-center");
                }
            });
            event.preventDefault();
        });

        $(".misc-section-settings").on('click', '.launch-daemon-test-stream', function(event) {
            $.ajax({
                url: "{{ route('interfaces.launch_daemon_teststream') }}",
                type: 'OPTIONS',
                async: true,
                beforeSend: function() {
                    $(".launch-daemon-test-stream").on('.test-streaming-btn-group').html("<span class='spinner-border spinner-border-sm me-1'></span>Launching Daemon").attr("disabled", true);
                   swal.fire({
                        title: "Launching Daemon For Test Streaming",
                        text: "Please wait",
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });
                    Swal.showLoading();
                },
                success: function(data) {
                    $(".launch-daemon-test-stream").on('.test-streaming-btn-group').html("<span class='bi bi-cpu me-1'></span>Launch Daemon").attr("disabled", false);
                    swal.fire({
                        icon: data.alert.icon,
                        title: data.alert.title,
                        text: data.alert.text,
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                    $('meta[name="csrf-token"]').val(data.csrftoken);
                    $('input[name=_token]').val(data.csrftoken);
                },
                error: function(err) {
                    $(".launch-daemon-test-stream").on('.test-streaming-btn-group').html("<span class='bi bi-cpu me-1'></span>Launch Daemon").attr("disabled", false);
                    swal.fire("Launching Daemon Test Streaming Failed", "There have problem while launching daemon test streaming!", "error");
                }
            });
            event.preventDefault();
        });
    });
</script>
<script>
    function msgalert(sector, msg) {
    $(sector).show();
    $(sector).find('ul').children().remove();
    $(sector).html('<ul></ul>').addClass("alert alert-danger");
    $.each(msg, function(key, value) {
        $(sector).find("ul").append('<li>' + value + '</li>');
    });
}
</script>
@endsection