<div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel"><span class="bi bi-play-btn me-1"></span>Edit
        Premiere
        Video
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <form class="form-edit-premiere-video">
        <div class="form-group mb-2">
            <label class="form-label">Name Premiere Video</label>
            <input type="text" class="form-control" name="name_video" placeholder="Name Video"
                value="{{ $premiere_video_data->title_video }}">
        </div>
        <div class="form-group mb-2">
            <label class="form-label">Local Path Video</label>
            <input type="text" class="form-control" name="path_video" placeholder="Path Video"
                value="{{ $premiere_video_data->video_path }}">
            <small class="form-text text-muted">*For path video please remove quote (").</small>
        </div>
        <div class="form-group mb-2">
            <label class="form-label me-1">Status Premiere Video</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="status-video-1" value="1" name="status_video" {{
                    $premiere_video_data->active_premiere_video == TRUE ? 'checked' : '' }}>
                <label class="form-check-label" for="status-video-1">Enable</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="status-video-2" value="0" name="status_video" {{
                    $premiere_video_data->active_premiere_video == FALSE ? 'checked' : '' }}>
                <label class="form-check-label" for="status-video-2">Disable</label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-edit-premiere-video"><span
                class="bi bi-save me-1"></span>Save</button>
    </form>
    <div class="my-2 edit-premiere-video-info-data"></div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
<script>
    $('input[name="path_video"]').on('keyup keypress change', function(e) {
        var value = $(this).val();
        if (value.indexOf('"') != -1) {
            $(this).val(value.replace(/\"/g, ""));
        }
    });
</script>