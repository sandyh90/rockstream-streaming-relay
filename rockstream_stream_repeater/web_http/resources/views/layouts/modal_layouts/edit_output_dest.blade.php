<div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel"><span class="material-icons me-1">cell_tower</span>Edit Output
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <form class="form-edit-output-dest">
        <div class="form-group mb-2">
            <label class="form-label">Name Output Destination</label>
            <input type="text" class="form-control" name="name_output_dest" placeholder="Name Output Destination"
                value="{{ $output_dest_data->name_stream_dest }}">
        </div>
        <div class="form-group mb-2">
            <label class="form-label">Platform Output</label>
            <select class="form-select" name="platform_output_dest">
                <option selected>- Select Platform -</option>
                <option value="youtube" {{ $output_dest_data->platform_dest == 'youtube' ? 'selected' : '' }}>Youtube
                </option>
                <option value="twitch" {{ $output_dest_data->platform_dest == 'twitch' ? 'selected' : '' }}>Twitch
                </option>
                <option value="custom" {{ $output_dest_data->platform_dest == 'custom' ? 'selected' : '' }}>Custom
                </option>
            </select>
        </div>
        <div class="form-group mb-2">
            <label class="form-label">RTMP Output Server</label>
            <input type="text" class="form-control" name="rtmp_output_server" placeholder="rtmp://xxx.xxx.xxx.xxx/live"
                value="{{ $output_dest_data->url_stream_dest }}">
            <small class="form-text text-muted">*For now we currently not support rtmps:// url server</small>
        </div>
        <div class="form-group mb-2">
            <label class="form-label">RTMP Stream Key</label>
            <div class="input-group stream-key-toggle-edit">
                <input type="password" class="form-control" name="rtmp_stream_key"
                    value="{{ $output_dest_data->key_stream_dest }}">
                <button type="button" class="input-group-text btn-toggle-stream-key" data-bs-toggle="tooltip"
                    data-bs-original-title="Show Stream Key"><span class="material-icons">visibility_off</span></button>
            </div>
        </div>
        <div class="form-group mb-2">
            <label class="form-label me-1">Status Output</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="edit-status-output-dest-1" value="1"
                    name="status_output_dest" {{ $output_dest_data->active_stream_dest == TRUE ? 'checked' : '' }}>
                <label class="form-check-label" for="edit-status-output-dest-1">Enable</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="edit-status-output-dest-2" value="0"
                    name="status_output_dest" {{ $output_dest_data->active_stream_dest == FALSE ? 'checked' : '' }}>
                <label class="form-check-label" for="edit-status-output-dest-2">Disable</label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-edit-output-dest"><span
                class="material-icons me-1">save</span>Save</button>
    </form>
    <div class="my-2 edit-output-dest-info-data"></div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
<script>
    $(".stream-key-toggle-edit button.btn-toggle-stream-key").on("click", function (event) {
        event.preventDefault();
        if ($(".stream-key-toggle-edit input").attr("type") == "text") {
            $(".stream-key-toggle-edit input").attr("type", "password");
            $(".stream-key-toggle-edit button.btn-toggle-stream-key span").html("visibility_off");
        } else if ($(".stream-key-toggle-edit input").attr("type") == "password") {
            $(".stream-key-toggle-edit input").attr("type", "text");
            $(".stream-key-toggle-edit button.btn-toggle-stream-key span").html("visibility");
        }
    });
</script>