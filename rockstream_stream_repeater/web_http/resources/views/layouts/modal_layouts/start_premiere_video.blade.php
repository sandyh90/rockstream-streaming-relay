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
                            <span class="material-icons me-1">sensors</span>Premiere
                        </div>' :'<div class="text-danger">
                            <span class="material-icons me-1">sensors_off</span>Offline</div') !!}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <h5 class="fw-light"><span class="bi bi-sliders me-1"></span>Start Premiere Video</h5>
    <hr>
    <div class="premiere-video-data-form">
        <form class="form-start-premiere-video">
            <div class="form-group mb-2">
                <label class="form-label">RTMP Input</label>
                <select class="custom-select rtmp-premiere-input" name="rtmp_premiere_input" data-width="100%">
                </select>
            </div>
            <div class="form-group mb-2">
                <label class="form-label">Bitrate Video</label>
                <input type="number" class="form-control" name="bitrate_premiere_video" placeholder="Bitrate Video">
                <small class="form-text text-muted">*Use kilobit format (2Mbps = 2000Kbps).</small>
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