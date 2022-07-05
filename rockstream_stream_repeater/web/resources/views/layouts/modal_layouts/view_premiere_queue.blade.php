<div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel"><span class="bi bi-collection-play me-1"></span>View Premiere Queue
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <h5 class="fw-light"><span class="bi bi-file-text me-1"></span>Premiere Queue Data</h5>
    <hr>
    <div class="table-responsive">
        <table class="table table-bordered" style="width:100%">
            <tbody>
                <tr>
                    <th scope="col"><span class="bi bi-type me-1"></span>Jobs Name</th>
                    <td>{{ json_decode($premiere_queue_data->payload)->displayName }}</td>
                </tr>
                <tr>
                    <th scope="col"><span class="bi bi-files-alt me-1"></span>Queue Name</th>
                    <td>{{ $premiere_queue_data->queue }}</td>
                </tr>
                <tr>
                    <th scope="col"><span class="bi bi-clock-history me-1"></span>Running At</th>
                    <td>{{ $premiere_queue_data->reserved_at ?
                        \Carbon\Carbon::createFromTimestamp($premiere_queue_data->reserved_at)->format('m-d-Y H:i:s') :
                        '-'; }}</td>
                </tr>
                <tr>
                    <th scope="col"><span class="bi bi-hourglass-split me-1"></span>Scheduled At</th>
                    <td>{{ $premiere_queue_data->available_at ?
                        \Carbon\Carbon::createFromTimestamp($premiere_queue_data->available_at)->format('m-d-Y H:i:s') :
                        '-'; }}</td>
                </tr>
                <tr>
                    <th scope="col"><span class="bi bi-clock me-1"></span>Created At</th>
                    <td>{{ $premiere_queue_data->created_at ?
                        \Carbon\Carbon::createFromTimestamp($premiere_queue_data->created_at)->format('m-d-Y H:i:s') :
                        '-'; }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>