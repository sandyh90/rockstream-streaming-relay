<div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel"><span class="bi bi-wrench-adjustable-circle me-1"></span>View
        Failed Queue
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <h5 class="fw-light"><span class="bi bi-file-text me-1"></span>Queue Data</h5>
    <hr>
    <div class="table-responsive">
        <table class="table table-bordered" style="width:100%">
            <tbody>
                <tr>
                    <th scope="col"><span class="bi bi-type me-1"></span>Jobs Name</th>
                    <td>{{ json_decode($failed_queue_data->payload)->displayName }}</td>
                </tr>
                <tr>
                    <th scope="col"><span class="bi bi-files-alt me-1"></span>Queue Name</th>
                    <td>{{ $failed_queue_data->queue }}</td>
                </tr>
                <tr>
                    <th scope="col"><span class="bi bi-clock me-1"></span>Failed At</th>
                    <td>{{ $failed_queue_data->failed_at }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <h5 class="fw-light my-2"><span class="bi bi-list-columns me-1"></span>Failed Detail</h5>
    <hr>
    <div class="failed-queue-data-detail">
        <dl>
            <dt>Payload:</dt>
            <dd class="text-break text-wrap">
                <pre><code>{!!
                htmlspecialchars(json_decode($failed_queue_data->payload)->data->command);
                !!}</code></pre>
            </dd>
            <dt>Exception:</dt>
            <dd class="text-break text-wrap">
                <button class="btn btn-primary btn-sm" data-bs-toggle="collapse" data-bs-target=".view-all-exception"
                    aria-expanded="false" aria-controls="view-all-exception"><span
                        class="bi bi-terminal me-1"></span>View Full Exception</button>
                <div class="collapse view-all-exception my-2">
                    <pre>{!! htmlspecialchars($failed_queue_data->exception) !!}</pre>
                </div>
            </dd>
        </dl>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>