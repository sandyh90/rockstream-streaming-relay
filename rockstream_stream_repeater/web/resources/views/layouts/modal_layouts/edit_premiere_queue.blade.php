<div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel"><span class="bi bi-collection-play me-1"></span>Edit Premiere Queue
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
    <h5 class="fw-light my-2"><span class="bi bi-stopwatch me-1"></span>Edit Schedule</h5>
    <hr>
    <form class="form-edit-premiere-queue">
        <div class="form-group mb-2">
            <label class="form-label">Schedule Datetime Premiere</label>
            <input type="datetime-local" class="form-control" name="schedule_datetime_premiere_video"
                pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}"
                value="{{ \Carbon\Carbon::createFromTimestamp($premiere_queue_data->available_at)->format('Y-m-d\TH:i') }}">
        </div>
        <label class="form-label">Time Zone<span class="bi bi-info-circle ms-1" data-bs-toggle="tooltip"
                data-bs-placement="top" title="Server Current: {{
            '('.\Carbon\Carbon::now()->getOffsetString().') '.\Carbon\Carbon::now()->timezoneName
            }}"></span></label>
        <div class="input-group mb-2">
            <div class="input-group-text">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="use-custom-timezone-schedule"
                        name="use_custom_timezone_premiere_schedule" value="1">
                    <label class="form-check-label" for="use-custom-timezone-schedule">Custom
                        Time Zone</label>
                </div>
            </div>
            <select class="form-select" name="custom_timezone_premiere_schedule">
                <option value="">Select Time Zone</option>
                @foreach (\App\Component\Utility::timezone_gmt_list() as $key => $timezone)
                <option value="{{ $key }}">{{ $timezone }}</option>
                @endforeach
            </select>
            <small class="form-text text-muted">*If you uncheck <strong>Custom Time Zone</strong>,
                your premiere will be scheduled using the time based on the time zone in the
                <strong>app</strong> or
                <strong>environment app</strong>.</small>
        </div>
        <button type="submit" class="btn btn-primary btn-edit-premiere-queue"><span
                class="bi bi-save me-1"></span>Save</button>
    </form>
    <div class="my-2 edit-premiere-queue-info-data"></div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>