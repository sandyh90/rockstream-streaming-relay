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
                    <video class="video-stream-preview" autoplay muted></video>
                </div>
                <div class="card card-body stream-info-status my-2" style="display: none;">
                </div>
            </div>
        </div>
        <div class="col-xl-6 mb-4">
            <div class="card card-body text-center">
                @if(Auth::user()->is_operator == TRUE)
                <div class="fs-4 fw-light my-2"><span class="bi bi-sliders me-1 fs-2"></span>Control Panel
                </div>
                <div class="control-action-stream-btn">
                    @if(\App\Component\Utility::getInstanceRunByPath((\App\Component\Utility::defaultBinDirFolder(config('component.nginx_path'))
                    . DIRECTORY_SEPARATOR . 'nginx.exe'))['found_process'] == TRUE)
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
                @endif
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
                        <div>
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
                                        <input type="password" class="form-control stream-key-val"
                                            placeholder="Stream Key" readonly x-bind:type="input">
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
                                <div class="text-center mb-2">
                                    <label>Stream QR Code</label>
                                    <div class="qr-code-container">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="dropdown">
                                <button class="btn btn-primary btn-sm dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="bi bi-journal-bookmark-fill me-1"></span>Manual Usage
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <div role="button" class="dropdown-item" data-bs-toggle="modal"
                                            data-bs-target=".modal-connect-lan-network">How to connect stream from local
                                            lan
                                            network</div>
                                    </li>
                                    <li>
                                        <div role="button" class="dropdown-item" data-bs-toggle="collapse"
                                            data-bs-target=".how-to-stream-encoder">How to Setup Stream Encoder</div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="collapse how-to-stream-encoder">
        <div class="card card-body">
            @include('layouts.info_layouts.how_to_stream_encoder')
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
                    return "There's is no stream input available";
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

        // Fetch Stream Key

        function fetch_stream_key() {
            $.ajax({
                type: "POST",
                url: "{{ route('panel.rtmp_stream_key') }}",
                data: {
                    input_stream: window.stream_input_id
                },
                async: true,
                success: function(data) {
                    $('meta[name="csrf-token"]').val(data.csrftoken);
                    if (data.success == true) {
                        if($(".encoder-setting-data div.credentials-input-stream").hide()){
                            $(".encoder-setting-data div.credentials-input-stream").show();
                        }
                        $(".video-preview-source div.offline-preview-title").html("<div class='spinner-border'></div><div class='fw-light'>Waiting Data From Encoder</div>");
                        $(".encoder-setting-data input.stream-url-val").val(data.stream_data.stream_url);
                        $(".encoder-setting-data input.stream-key-val").val(data.stream_data.stream_key);
                        $(".encoder-setting-data div.qr-code-container").html(data.stream_data.stream_qrcode);
                    }
                },
                error: function(data) {
                }
            });
        }

    @if(Auth::user()->is_operator == TRUE)

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
                    $('meta[name="csrf-token"]').val(data.csrftoken);
                },
                error: function(err) {
                    swal.fire("Send Control Signal Failed", "There have problem while send control signal!", "error");
                }
            });
            event.preventDefault();
        });

    @endif
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
                    video.setAttribute('controls', '');
                    video.play();
                });
            } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                video.src = video_url;
                video.addEventListener('loadedmetadata', function () {
                    video.setAttribute('controls', '');
                    video.play();
                });
            }
        }

        function reset_video_preview() {
            hls.stopLoad();
            hls.detachMedia();
            video.pause();
            video.removeAttribute('src');
            video.removeAttribute('controls');
            video.load();
        }

        hls.on(Hls.Events.ERROR, function (event, data) {
            if(data.details === Hls.ErrorDetails.LEVEL_LOAD_ERROR && data.type === Hls.ErrorTypes.NETWORK_ERROR) {
                reset_video_preview();
                $("div.stream-info-status").html("<div class='fs-5 text-center text-danger'><span class='bi bi-exclamation-triangle fs-2 me-1'></span>Failed To Connect Stream, Stream Offline Or Server Down</div>").show();
                if($(".video-preview-source div.offline-preview").hide()){
                    $(".video-preview-source div.offline-preview").show();
                }
                $(".video-preview-source div.offline-preview-title").html("<div class='bi bi-exclamation-triangle fs-2'></div><div class='fw-light'>Failed To Connect Stream</div>");
                setTimeout('check_stream_preview();', 5000);
            }
        });
    }

    // Check Stream Preview
    
    function check_stream_preview() {
        $.ajax({
            type: "POST",
            url: "{{ route('panel.rtmp_preview') }}",
            data: {
                input_stream: window.stream_input_id
            },
            async: true,
            success: function(data) {
                $('meta[name="csrf-token"]').val(data.csrftoken);
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
                    $("div.stream-info-status").html("<div class='fs-5 text-center text-danger'><span class='bi bi-exclamation-triangle fs-2 me-1'></span>Connection Failed, Retry Getting Data.</div>").show();
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

@section('modal-content')
<div class="modal fade modal-connect-lan-network" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    role="dialog">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content rounded-6 shadow">
            <div class="modal-body">
                @include('layouts.info_layouts.how_connect_lan_network')
                <button type="button" class="btn btn-primary btn-dismiss-connect-lan-network-modal mt-2 w-100"
                    data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
@endsection