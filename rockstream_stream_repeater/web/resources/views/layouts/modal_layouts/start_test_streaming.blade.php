<div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel"><span class="bi bi-broadcast me-1"></span>Start
        Test Streaming</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <h5 class="fw-light"><span class="bi bi-sliders me-1"></span>Start Streaming Test</h5>
    <hr>
    <div class="test-streaming-data-form">
        <form class="form-start-test-streaming">
            <div class="form-group mb-2">
                <label class="form-label">RTMP Input</label>
                <select class="custom-select rtmp-premiere-input" name="rtmp_test_input" data-width="100%">
                </select>
            </div>
            <div class="form-group mb-2">
                <label class="form-label">Encoder Type Video</label>
                <select class="form-select" name="encoder_type_video">
                    <option selected>Select Encoder Video</option>
                    <option value="libx264">Encode Only With CPU</option>
                    <option value="h264_qsv">Intel (QuickSync)</option>
                    <option value="h264_nvenc">Nvidia (CUDA)</option>
                    <option value="h264_amf">AMD (AMF [Advanced Media Framework])</option>
                </select>
                <small class="form-text text-muted">*Careful: use encoder type that's compatible with your GPU, If you
                    choose wrong GPU type, encoder will failed to process test stream.</small>
            </div>
            <div class="form-group mb-2">
                <label class="form-label">FPS Video</label>
                <select class="form-select" name="fps_type_video">
                    <option selected>Select FPS Video</option>
                    <option value="30">30 FPS</option>
                    <option value="40">40 FPS</option>
                    <option value="50">50 FPS</option>
                    <option value="60">60 FPS</option>
                </select>
            </div>
            <div class="form-group mb-2">
                <label class="form-label">Bitrate Video</label>
                <input type="number" class="form-control" name="bitrate_test_video" min="1" max="50000"
                    placeholder="Bitrate Video">
                <small class="form-text text-muted">*Use kilobit format (2Mbps = 2000Kbps).</small>
            </div>
            <div class="form-group mb-2">
                <label class="form-label">Limit Duration</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" name="limit_duration_stream"
                        id="limit-duration-stream-test">
                    <label class="form-check-label" for="limit-duration-stream-test">
                        Limit Duration Streaming Test
                    </label>
                </div>
                <small class="form-text text-muted">*If you use this the streaming test duration will be limit to 10
                    Minutes</small>
            </div>
            <button type="submit" class="btn btn-primary btn-start-test-streaming"><span
                    class="bi bi-broadcast me-1"></span>Start Test</button>
        </form>
    </div>
    <div class="my-2 start-test-streaming-info-data"></div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
<script>
    $('.rtmp-premiere-input').select2({
        placeholder: 'Select Stream Input First',
        dropdownParent: '.custom-modal-display .test-streaming-data-form',
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