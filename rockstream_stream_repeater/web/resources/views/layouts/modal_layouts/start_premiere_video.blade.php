<div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel"><span class="bi bi-play-btn me-1"></span>Start
        Premiere Video</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <h5 class="fw-light"><span class="bi bi-file-text me-1"></span>Premiere Video Data</h5>
    <hr>
    <div class="table-responsive">
        <table class="table table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th scope="col"><span class="bi bi-type me-1"></span>Name Video</th>
                    <th scope="col"><span class="bi bi-broadcast me-1"></span>Premiere</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $premiere_video_data->title_video }}</td>
                    <td>{!! ($premiere_video_data->is_premiere == TRUE ? '<div class="text-success flash-text-item">
                            <span class="bi bi-play-circle me-1"></span>Premiere
                        </div>' :'<div class="text-danger">
                            <span class="bi bi-dash-circle me-1"></span>Offline</div') !!}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <h5 class="fw-light"><span class="bi bi-sliders me-1"></span>Start Premiere Video</h5>
    <hr>
    <div class="premiere-video-data-form">
        <form class="form-start-premiere-video">
            <div class="row">
                <div class="col-xl-6">
                    <div class="card card-body mb-2">
                        <div class="form-group mb-2">
                            <label class="form-label">RTMP Input</label>
                            <select class="custom-select rtmp-premiere-input" name="rtmp_premiere_input"
                                data-width="100%">
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label class="form-label">Encoder Type Video</label>
                            <select class="form-select" name="encoder_type_video">
                                <option value="">Select Encoder Video</option>
                                <option value="libx264">Encode Only With CPU</option>
                                <option value="h264_qsv">Intel (QuickSync)</option>
                                <option value="h264_nvenc">Nvidia (CUDA)</option>
                                <option value="h264_amf">AMD (AMF [Advanced Media Framework])</option>
                            </select>
                            <small class="form-text text-muted">*Careful: use encoder type that's compatible with your
                                GPU,
                                If you
                                choose wrong GPU type, encoder will failed to process premiere video.</small>
                        </div>
                        <div class="form-group mb-2">
                            <label class="form-label">Bitrate Video</label>
                            <input type="number" class="form-control" name="bitrate_premiere_video" min="1" max="50000"
                                placeholder="Bitrate Video">
                            <small class="form-text text-muted">*Use kilobit format (e.g 2Mbps = 2000Kbps).</small>
                        </div>
                    </div>
                    <div class="card card-body mb-2" x-data="{ open: false }">
                        <div class="form-group mb-2">
                            <label class="form-label">Use Countdown Video</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                    id="countdown-premiere-switch" name="countdown_premiere_video" value="1"
                                    x-on:click="open = ! open">
                                <label class="form-check-label" for="countdown-premiere-switch">Countdown Video</label>
                            </div>
                            <small class="form-text text-muted">*Countdown video will be show if premiere video start
                                before real video begin.</small>
                        </div>
                        <div class="form-group mb-2" x-show="open">
                            <label class="form-label">Custom Countdown Video</label>
                            <div class="input-group">
                                <div class="input-group-text">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="use-local-video-countdown"
                                            name="use_local_video_countdown" value="1">
                                        <label class="form-check-label" for="use-local-video-countdown">Use Local
                                            Video</label>
                                    </div>
                                </div>
                                <input type="text" class="form-control" name="local_video_countdown_path"
                                    placeholder="Local Video Path">
                            </div>
                            <small class="form-text text-muted">*For path video please remove quote (") if
                                present.</small>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card card-body mb-2" x-data="{ open: false }">
                        <div class="form-group mb-2">
                            <label class="form-label">Schedule Premiere [Beta]</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                    id="schedule-premiere-switch" name="schedule_premiere_video" value="1"
                                    x-on:click="open = ! open">
                                <label class="form-check-label" for="schedule-premiere-switch">Schedule Video
                                    Premiere</label>
                            </div>
                            <small class="form-text text-muted">*Video will be scheduled and automatically
                                premiering.</small>
                        </div>
                        <div class="form-group mb-2" x-show="open">
                            <label class="form-label">Schedule Datetime Premiere</label>
                            <input type="datetime-local" class="form-control" name="schedule_datetime_premiere_video"
                                pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}">
                            <small class="form-text text-muted">*You must change Timezone first if you wanna use this
                                schedule feature in <strong>.env</strong> file .</small>
                        </div>
                    </div>
                    <div class="card card-body mb-2" x-data="{ resolution_select: null }">
                        <div class="form-group mb-2">
                            <label class="form-label">Resolution Size</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="video_resolution_size"
                                    id="resolution-video-size-1" value="locked_resolution" x-model="resolution_select">
                                <label class="form-check-label" for="resolution-video-size-1">
                                    Locked resolution to 1920x1080 (1080p/1080i)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="video_resolution_size"
                                    id="resolution-video-size-2" value="follow_resolution" x-model="resolution_select">
                                <label class="form-check-label" for="resolution-video-size-2">
                                    Follow source video resolution
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="video_resolution_size"
                                    id="resolution-video-size-3" value="custom_resolution" x-model="resolution_select">
                                <label class="form-check-label" for="resolution-video-size-3">
                                    Custom video resolution
                                </label>
                            </div>
                            <div class="form-group my-2" x-show="resolution_select == 'custom_resolution'">
                                <div class="small text-muted"><span class="bi bi-aspect-ratio me-1"></span>Aspect Ratio:
                                    <span class="aspect-ratio-custom-resolution"></span>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text">Width</span>
                                    <input type="number" class="form-control" min="8" name="width_custom_resolution"
                                        placeholder="Width">
                                    <span class="input-group-text">Height</span>
                                    <input type="number" class="form-control" min="8" name="height_custom_resolution"
                                        placeholder="Height">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-start-premiere-video"><span
                    class="bi bi-broadcast me-1"></span>Start Premiere</button>
        </form>
    </div>
    <div class="my-2 start-premiere-video-info-data"></div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
<script>
    $('input[name="local_video_countdown_path"]').on('keyup keypress change', function(e) {
        var value = $(this).val();
        if (value.indexOf('"') != -1) {
            $(this).val(value.replace(/\"/g, ""));
        }else if (value.indexOf('\'') != -1) {
            $(this).val(value.replace(/\'/g, ""));
        }
    });

    $('input[name="width_custom_resolution"], input[name="height_custom_resolution"]').on('keyup keypress change', function(e) {
        function gcd (a, b) {
            return (b == 0) ? a : gcd (b, a % b);
        }
        var width = $('input[name="width_custom_resolution"]').val();
        var height = $('input[name="height_custom_resolution"]').val();
        var gcd_value = gcd(width, height);
        if(width !== null && height !== null && gcd_value !== null) {
            $('span.aspect-ratio-custom-resolution').text(`${width / gcd_value} : ${height / gcd_value}`);
        }else{
            $('span.aspect-ratio-custom-resolution').text('0 : 0');
        }
    });

    $('.rtmp-premiere-input').select2({
        placeholder: 'Select Stream Input First',
        dropdownParent: '.custom-modal-display .premiere-video-data-form',
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
            delay: 250,
            data: function(params) {
                return {
                    search: params.term // search term
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
</script>