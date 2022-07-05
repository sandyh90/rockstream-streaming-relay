<div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel"><span class="bi bi-broadcast me-1"></span>View Output
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <h5 class="fw-light"><span class="bi bi-file-text me-1"></span>Output Destination Data</h5>
    <hr>
    <div class="table-responsive">
        <table class="table table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th scope="col"><span class="bi bi-type me-1"></span>Name Output</th>
                    <th scope="col"><span class="bi bi-broadcast me-1"></span>Platform</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $output_dest_data->name_stream_dest }}</td>
                    <td>{!! \App\Component\ServicesCore::getServiceIcons($output_dest_data->platform_dest) !!}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <h5 class="fw-light"><span class="bi bi-list-columns me-1"></span>Destination Setting Data</h5>
    <hr>
    <div class="output-setting-data-detail">
        <div class="form-group mb-2">
            <label class="form-label">RTMP Output Server</label>
            <div class="input-group">
                <input type="text" class="form-control stream-url-val" name="rtmp_output_server"
                    placeholder="rtmp://xxx.xxx.xxx.xxx/live" value="{{ $output_dest_data->url_stream_dest }}" readonly>
                <button type="button" class="input-group-text btn-clipboard" data-bs-toggle="tooltip"
                    data-bs-original-title="Copy Stream URL" data-clipboard-action="copy"
                    data-clipboard-target=".output-setting-data-detail input.stream-url-val"><span
                        class="bi bi-clipboard"></span></button>
            </div>
        </div>
        <div class="form-group mb-2">
            <label class="form-label">RTMP Stream Key</label>
            <div class="input-group" x-data="{ input: 'password' }">
                <input type="password" class="form-control stream-key-val" name="rtmp_stream_key"
                    value="{{ $output_dest_data->key_stream_dest }}" readonly x-bind:type="input">
                <button type="button" class="input-group-text btn-toggle-stream-key" data-bs-toggle="tooltip"
                    data-bs-original-title="Show Stream Key"
                    x-on:click="input = (input === 'password') ? 'text' : 'password'"><span
                        :class="{'bi bi-eye-slash' : input != 'password','bi bi-eye': input != 'text'}"></span></button>
                <button type="button" class="input-group-text btn-clipboard" data-bs-toggle="tooltip"
                    data-bs-original-title="Copy Stream Key" data-clipboard-action="copy"
                    data-clipboard-target=".output-setting-data-detail input.stream-key-val"><span
                        class="bi bi-clipboard"></span></button>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
</script>