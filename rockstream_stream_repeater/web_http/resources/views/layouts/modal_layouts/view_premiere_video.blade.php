<div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel"><span class="bi bi-play-btn me-1"></span>View
        Premiere Video
    </h5>
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
    <h5 class="fw-light"><span class="bi bi-list-columns me-1"></span>Video Data</h5>
    <hr>
    <div class="premiere-video-data-detail">
        <dl>
            <dt>Path Video:</dt>
            <dd class="text-break text-wrap">{{ $premiere_video_data->video_path; }}</dd>
        </dl>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>