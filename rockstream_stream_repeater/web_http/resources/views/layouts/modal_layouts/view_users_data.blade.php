<div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel"><span class="bi bi-person-badge me-1"></span>View User
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <h5 class="fw-light"><span class="bi bi-person-lines-fill me-1"></span>User Data</h5>
    <hr>
    <div class="table-responsive">
        <table class="table table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th scope="col"><span class="bi bi-type me-1"></span>Name User</th>
                    <th scope="col"><span class="bi bi-person me-1"></span>Username</th>
                    <th scope="col"><span class="bi bi-wrench-adjustable-circle me-1"></span>Operator</th>
                    <th scope="col"><span class="bi bi-toggles me-1"></span>Active</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $users_data->name }}</td>
                    <td>{{ $users_data->username }}</td>
                    <td>{!! ($users_data->is_operator == TRUE ? '<div class="text-danger">
                            <span class="bi bi-wrench-adjustable-circle me-1"></span>Operator
                        </div>' :'<div class="text-success">
                            <span class="bi bi-person-circle me-1"></span>Non Operator</div') !!}</td>
                    <td>{!! ($users_data->is_active == TRUE ? '<div class="text-success">
                            <span class="bi bi-check-circle me-1"></span>Active
                        </div>' :'<div class="text-danger">
                            <span class="bi bi-x-circle me-1"></span>Not Active</div') !!}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>