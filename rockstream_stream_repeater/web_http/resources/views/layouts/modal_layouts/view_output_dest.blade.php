<div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel"><span class="material-icons me-1">cell_tower</span>View Output
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <h5 class="fw-light"><span class="material-icons me-1">description</span>Output Destination Data</h5>
    <hr>
    <div class="table-responsive">
        <table class="table table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th scope="col"><span class="material-icons me-1">spellcheck</span>Name Output</th>
                    <th scope="col"><span class="material-icons me-1">sensors</span>Platform</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $output_dest_data->name_stream_dest }}</td>
                    <td>{{ ($output_dest_data->platform_dest == 'youtube' ? 'Youtube' :
                        ($output_dest_data->platform_dest == 'twitch' ? 'Twitch' : ($output_dest_data->platform_dest ==
                        'custom' ? 'Custom' : 'Unknown'))) }}</td>
                </tr>
            </tbody>

        </table>
    </div>
    <h5 class="fw-light"><span class="material-icons me-1">cast</span>Destination Setting Data</h5>
    <hr>
    <div class="output-setting-data">
        <div class="form-group mb-2">
            <label class="form-label">RTMP Output Server</label>
            <div class="input-group">
                <input type="text" class="form-control stream-url-val" name="rtmp_output_server"
                    placeholder="rtmp://xxx.xxx.xxx.xxx/live" value="{{ $output_dest_data->url_stream_dest }}" readonly>
                <button type="button" class="input-group-text btn-clipboard" data-bs-toggle="tooltip"
                    data-bs-original-title="Copy Stream URL" data-clipboard-action="copy"
                    data-clipboard-target=".output-setting-data input.stream-url-val"><span
                        class="material-icons">content_paste</span></button>
            </div>
        </div>
        <div class="form-group mb-2">
            <label class="form-label">RTMP Stream Key</label>
            <div class="input-group stream-key-toggle-view">
                <input type="password" class="form-control stream-key-val" name="rtmp_stream_key"
                    value="{{ $output_dest_data->key_stream_dest }}" readonly>
                <button type="button" class="input-group-text btn-toggle-stream-key" data-bs-toggle="tooltip"
                    data-bs-original-title="Show Stream Key"><span class="material-icons">visibility_off</span></button>
                <button type="button" class="input-group-text btn-clipboard" data-bs-toggle="tooltip"
                    data-bs-original-title="Copy Stream Key" data-clipboard-action="copy"
                    data-clipboard-target=".output-setting-data input.stream-key-val"><span
                        class="material-icons">content_paste</span></button>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
<script>
    $(".stream-key-toggle-view button.btn-toggle-stream-key").on("click", function (event) {
        event.preventDefault();
        if ($(".stream-key-toggle-view input").attr("type") == "text") {
            $(".stream-key-toggle-view input").attr("type", "password");
            $(".stream-key-toggle-view button.btn-toggle-stream-key span").html("visibility_off");
        } else if ($(".stream-key-toggle-view input").attr("type") == "password") {
            $(".stream-key-toggle-view input").attr("type", "text");
            $(".stream-key-toggle-view button.btn-toggle-stream-key span").html("visibility");
        }
    });

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
</script>