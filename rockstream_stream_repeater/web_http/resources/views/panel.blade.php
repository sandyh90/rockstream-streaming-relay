@extends('layouts.main')

@section('title','Dashboard Panel')

@section('head-content')

<link rel="stylesheet" href="{{ asset('assets/vendor/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/select2/css/select2-bootstrap4.min.css') }}">
<link href="{{ asset('assets/vendor/zoomify/zoomify.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xl-6 mb-4 mx-auto">
            <div class="card card-body text-center">
                <div class="fs-4 fw-light my-2"><span class="bi bi-play-btn me-1 fs-2"></span>Preview</div>
                <div class="ratio ratio-16x9 video-preview-source">
                    <div class="offline-preview overlay-baseplate video-overlay-base">
                        <div class="offline-preview-title overlay-title">
                            <div class="bi bi-hdmi fs-2"></div>
                            <div class="fw-light">Select Input Streaming First</div>
                        </div>
                    </div>
                    <video class="video-stream-preview" controls autoplay muted></video>
                </div>
                <div class="card card-body stream-info-status my-2" style="display: none;">
                </div>
            </div>
        </div>
        <div class="col-xl-6 mb-4">
            <div class="card card-body text-center">
                <div class="fs-4 fw-light my-2"><span class="bi bi-sliders me-1 fs-2"></span>Control Panel
                </div>
                <div class="control-action-stream-btn">
                    @if (\App\Component\Utility::getInstanceRun('nginx.exe') == TRUE)
                    <div class="btn btn-danger control-stream-btn my-2" control-stream-action="power"><span
                            class="bi bi-power me-1"></span>Stop Server
                    </div>
                    @else
                    <div class="btn btn-success control-stream-btn my-2" control-stream-action="power"><span
                            class="bi bi-power me-1"></span>Start
                        Server
                    </div>
                    @endif
                    <div class="btn btn-warning control-stream-btn my-2" control-stream-action="disable"><span
                            class="bi bi-cloud-slash me-1"></span>Disable Stream
                    </div>
                    <div class="btn btn-primary control-stream-btn my-2" control-stream-action="restart"><span
                            class="bi bi-arrow-clockwise me-1"></span>Restart Server
                    </div>
                    <div class="btn btn-secondary control-stream-btn my-2" control-stream-action="regenerate"><span
                            class="bi bi-arrow-repeat me-1"></span>Regen Config Stream [Manual]
                    </div>
                </div>
                <div class="control-info-status my-2"></div>
                <div class="card card-body">
                    <div class="fs-4 fw-light my-2"><span class="bi bi-pc-display me-1 fs-2"></span>Setup Your
                        Encoder
                    </div>
                    <div class="encoder-setting-data">
                        <div class="form-group mb-2">
                            <label for="form-label">Select Stream Input</label>
                            <select class="form-select stream-input-select" data-width="100%">
                            </select>
                        </div>
                        <div class="credentials-input-stream" style="display: none;">
                            <div class="form-group mb-2">
                                <label for="form-label">Stream URL</label>
                                <div class="input-group">
                                    <input type="text" class="form-control stream-url-val" placeholder="Stream URL"
                                        readonly>
                                    <button type="button" class="input-group-text btn-clipboard"
                                        data-bs-toggle="tooltip" data-bs-original-title="Copy Stream URL"
                                        data-clipboard-action="copy"
                                        data-clipboard-target=".encoder-setting-data input.stream-url-val"><span
                                            class="bi bi-clipboard"></span></button>
                                </div>
                            </div>
                            <div class="form-group mb-2">
                                <label for="form-label">Stream Key</label>
                                <div class="input-group" x-data="{ input: 'password' }">
                                    <input type="password" class="form-control stream-key-val" placeholder="Stream Key"
                                        readonly x-bind:type="input">
                                    <button type="button" class="input-group-text btn-toggle-stream-key"
                                        data-bs-toggle="tooltip" data-bs-original-title="Show Stream Key"
                                        x-on:click="input = (input === 'password') ? 'text' : 'password'"><span
                                            :class="{'bi bi-eye-slash' : input != 'password','bi bi-eye': input != 'text'}"></span></button>
                                    <button type="button" class="input-group-text btn-clipboard"
                                        data-bs-toggle="tooltip" data-bs-original-title="Copy Stream Key"
                                        data-clipboard-action="copy"
                                        data-clipboard-target=".encoder-setting-data input.stream-key-val"><span
                                            class="bi bi-clipboard"></span></button>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="collapse"
                                data-bs-target=".how-to-stream-encoder">
                                <span class="bi bi-journal-bookmark-fill me-1"></span>How to Setup Stream Encoder
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="collapse how-to-stream-encoder">
        <div class="card card-body">
            <div class="card-title text-center"><span class="bi bi-journal-bookmark-fill me-1"></span>How to Setup
                Stream
                Encoder</div>
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                    <img src="{{ asset('assets/img/OBS-Logo.png') }}" width="45px" alt="OBS Logo">
                </div>
                <div class="flex-grow-1 ms-3 fw-bold">
                    OBS (Open Broadcaster Software) Encoder
                </div>
            </div>
            <hr>
            <small class="text-muted">*This setup process maybe different if software get updated to latest
                version</small>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <div class="col">
                    <div class="card">
                        <img loading="lazy" class="card-img-top how-to-img"
                            src="{{ asset('assets/img/how-to-setup/obs-1.png') }}" width="100%" alt="OBS Step 1">
                        <div class="card-body">
                            <h5 class="card-title">Step 1</h5>
                            <p class="card-text">After you opened OBS (Open Broadcaster Software) goto
                                <strong>"Settings"</strong>
                                button.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <img loading="lazy" class="card-img-top how-to-img"
                            src="{{ asset('assets/img/how-to-setup/obs-2.png') }}" width="100%" alt="OBS Step 2">
                        <div class="card-body">
                            <h5 class="card-title">Step 2</h5>
                            <p class="card-text">If window <strong>"Settings"</strong> opened, select
                                <strong>"Stream"</strong> menu.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <img loading="lazy" class="card-img-top how-to-img"
                            src="{{ asset('assets/img/how-to-setup/obs-3.png') }}" width="100%" alt="OBS Step 3">
                        <div class="card-body">
                            <h5 class="card-title">Step 3</h5>
                            <p class="card-text">Next step in service section select <strong>"Custom..."</strong>, then
                                fill server url
                                and stream key that be given in web,
                                after you filled server url and stream key then click <strong>"Apply"</strong> button
                                and then click <strong>"OK"</strong>
                                button.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                    <img src="{{ asset('assets/img/StreamLabs-Logo.png') }}" width="45px" alt="StreamLabs Logo">
                </div>
                <div class="flex-grow-1 ms-3 fw-bold">
                    StreamLabs Encoder
                </div>
            </div>
            <hr>
            <small class="text-muted">*This setup process maybe different if software get updated to latest
                version</small>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <div class="col">
                    <div class="card">
                        <img loading="lazy" class="card-img-top how-to-img"
                            src="{{ asset('assets/img/how-to-setup/streamlabs-1.png') }}" width="100%"
                            alt="StreamLabs Step 1">
                        <div class="card-body">
                            <h5 class="card-title">Step 1</h5>
                            <p class="card-text">After you opened StreamLabs goto
                                <strong>"Gear<span class="bi bi-gear-fill ms-1"></span>"</strong>
                                icon.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <img loading="lazy" class="card-img-top how-to-img"
                            src="{{ asset('assets/img/how-to-setup/streamlabs-2.png') }}" width="100%"
                            alt="StreamLabs Step 2">
                        <div class="card-body">
                            <h5 class="card-title">Step 2</h5>
                            <p class="card-text">If window <strong>"Settings"</strong> opened, select
                                <strong>"Stream"</strong> menu.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <img loading="lazy" class="card-img-top how-to-img"
                            src="{{ asset('assets/img/how-to-setup/streamlabs-3.png') }}" width="100%"
                            alt="StreamLabs Step 3">
                        <div class="card-body">
                            <h5 class="card-title">Step 3</h5>
                            <p class="card-text">Next step in stream type section select <strong>"Custom Streaming
                                    Server"</strong>, then fill server url and stream key that be given in web,
                                after you filled server url and stream key then click <strong>"Done"</strong> button.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                    <div class="bi bi-person-video fs-1"></div>
                </div>
                <div class="flex-grow-1 ms-3 fw-bold">
                    Other Encoder
                </div>
            </div>
            <hr>
            <p>If your software encoder support custom RTMP, you just input server input stream point and input stream
                key.</p>
        </div>
    </div>
</div>
@endsection

@section('js-content')
<script src="{{ asset('assets/vendor/hls.js/hls.min.js') }}"></script>
<script src="{{ asset('assets/vendor/select2/js/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/vendor/zoomify/zoomify.min.js') }}"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        $('img.how-to-img').zoomify();

        $('.stream-input-select').select2({
            placeholder: 'Select Stream Input First',
            theme: 'bootstrap4',
            language: {
                noResults: function (params) {
                    return "There is no stream input available";
                }
            },
            ajax: {
                url: "{{ route('panel.fetch_stream_input') }}",
                type: 'POST',
                dataType: 'json',
                async: true,
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });

        $('.stream-input-select').on('select2:select', function(e) {
            var input_stream_id = e.params.data.id
            if (input_stream_id === null) return;
            window.stream_input_id = input_stream_id;
            check_stream_preview();
            fetch_stream_key();
        });

        $(".control-action-stream-btn").on('click', '.control-stream-btn', function(event) {
            var action_control = $(event.currentTarget).attr('control-stream-action');
            if (action_control === null) return;
            $.ajax({
                url: "{{ route('home.control_server') }}",
                type: 'POST',
                data: {
                    action_fetch: action_control
                },
                async: true,
                beforeSend: function() {
                    $(event.currentTarget).append("<span class='spinner-border spinner-border-sm ms-1'></span>").attr("disabled", true);
                    swal.fire({
                        title: "Control Signal Sended",
                        text: "Please wait",
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });
                    Swal.showLoading();
                },
                success: function(data) {
                    if (data.success == true) {
                        $(".control-info-status").html(data.msg).show().delay(3000).fadeOut();
                        $('.control-action-stream-btn').load(location.href + " .control-action-stream-btn");
                        swal.close();
                    }
                    $(event.currentTarget).attr("disabled", false);
                    $(".control-stream-btn span.spinner-border").remove();
                    $('meta[name="csrf-token"').val(data.csrftoken);
                },
                error: function(err) {
                    swal.fire("Send Control Signal Failed", "There have problem while send control signal!", "error");
                }
            });
            event.preventDefault();
        });
    });
</script>
<script>
    // Video component for stream preview.
    if (typeof Hls != 'undefined') {
        var video = document.querySelector('.video-stream-preview');
        var hls = new Hls();
        function video_preview_start(video_url) {
            if (Hls.isSupported()) {
                hls.loadSource(video_url);
                hls.attachMedia(video);
                hls.on(Hls.Events.MANIFEST_PARSED, function () {
                    video.play();
                });
            } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                video.src = video_url;
                video.addEventListener('loadedmetadata', function () {
                    video.play();
                });
            }
        }

        function reset_video_preview() {
            hls.stopLoad();
            hls.detachMedia();
            video.pause();
            video.removeAttribute('src');
            video.load();
        }

        hls.on(Hls.Events.ERROR, function (event, data) {
            if(data.details === Hls.ErrorDetails.LEVEL_LOAD_ERROR && data.type === Hls.ErrorTypes.NETWORK_ERROR) {
                $("div.stream-info-status").html("<div class='fs-5 text-center text-danger'><span class='bi bi-exclamation-triangle fs-2 me-1'></span>Failed To Connect Stream, Stream Offline Or Server Down</div>").show();
                if($(".video-preview-source div.offline-preview").hide()){
                    $(".video-preview-source div.offline-preview").show();
                }
                $(".video-preview-source div.offline-preview-title").html("<div class='bi bi-exclamation-triangle fs-2'></div><div class='fw-light'>Failed To Connect Stream</div>");
                reset_video_preview();
                setTimeout('check_stream_preview();', 5000);
            }
        });
    }

    // Check Stream Preview and Fetch Stream Key

    function fetch_stream_key() {
        $.ajax({
            type: "POST",
            url: "{{ route('panel.rtmp_stream_key') }}",
            data: {
                input_stream: window.stream_input_id
            },
            async: true,
            success: function(data) {
                if (data.success == true) {
                    if($(".encoder-setting-data div.credentials-input-stream").hide()){
                        $(".encoder-setting-data div.credentials-input-stream").show();
                    }
                    $(".video-preview-source div.offline-preview-title").html("<div class='spinner-border'></div><div class='fw-light'>Waiting Data From Encoder</div>");
                    $(".encoder-setting-data input.stream-url-val").val(data.input_stream.stream_url);
                    $(".encoder-setting-data input.stream-key-val").val(data.input_stream.stream_key);
                } else {
                }
            },
            error: function(data) {
            }
        });
    }
    function check_stream_preview() {
        $.ajax({
            type: "POST",
            url: "{{ route('panel.rtmp_preview') }}",
            data: {
                input_stream: window.stream_input_id
            },
            async: true,
            success: function(data) {
                if (data.success == true) {
                    if (data.is_live == true) {
                        $("div.stream-info-status").html("<div class='fs-5 text-center text-success'><span class='bi bi-check2-circle fs-2 me-1'></span>Successfuly Getting Data From Encoder.</div>").show();
                        if($(".video-preview-source div.offline-preview").show()){
                            $(".video-preview-source div.offline-preview").hide();
                        }
                        $(".video-preview-source div.offline-preview div.offline-preview-title").empty();
                        video_preview_start(data.stream_url);
                    }else{
                        $("div.stream-info-status").html("<div class='fs-5 text-center text-danger'><span class='bi bi-exclamation-triangle fs-2 me-1'></span>No Streaming Data Input.</div>").show();
                        if($(".video-preview-source div.offline-preview").hide()){
                            $(".video-preview-source div.offline-preview").show();
                        }
                        $(".video-preview-source div.offline-preview-title").html("<div class='spinner-border'></div><div class='fw-light'>Waiting Data From Encoder</div>");
                        reset_video_preview();
                        setTimeout('check_stream_preview();', 5000);
                    }
                } else {
                    $("div.stream-info-status").html("<div class='fs-5 text-center text-danger'><span class='bi bi-exclamation-triangle fs-2 me-1'></span>Connection Failed, Retry Data.</div>").show();
                    if($(".video-preview-source div.offline-preview").hide()){
                        $(".video-preview-source div.offline-preview").show();
                    }
                    setTimeout('check_stream_preview();', 5000);
                }
            },
            error: function(data) {
                setTimeout('check_stream_preview();', 5000);
            }
        });
    }
</script>
@endsection