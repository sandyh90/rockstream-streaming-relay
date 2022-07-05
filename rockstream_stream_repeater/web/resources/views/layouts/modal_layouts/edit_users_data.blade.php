<div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel"><span class="bi bi-pencil-square me-1"></span>Edit User
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <form class="form-edit-users">
        <div class="form-group mb-2">
            <label class="form-label">Name</label>
            <input type="text" class="form-control" name="name_user" value="{{ $users_data['name'] }}"
                placeholder="Name">
        </div>
        <div class="form-group mb-2">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" name="username_user" value="{{ $users_data['username'] }}"
                placeholder="Username">
        </div>
        <div class="form-group mb-2">
            <label class="form-label">Password</label>
            <div class="input-group" x-data="{ input: 'password' }">
                <input type="password" class="form-control" name="password_user" placeholder="Password"
                    x-bind:type="input">
                <button type="button" class="input-group-text"
                    x-on:click="input = (input === 'password') ? 'text' : 'password'"><span
                        :class="{'bi bi-eye-slash' : input != 'password','bi bi-eye': input != 'text'}"></span></button>
            </div>
        </div>
        <div class="form-group mb-2">
            <label class="form-label me-1">Operator User</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="operator-user-1" value="1" name="operator_user" {{
                    $users_data['is_operator']==TRUE ? 'checked' : '' }}>
                <label class="form-check-label" for="operator-user-1">Operator</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="operator-user-2" value="0" name="operator_user" {{
                    $users_data['is_operator']==FALSE ? 'checked' : '' }}>
                <label class="form-check-label" for="operator-user-2">Non Operator</label>
            </div>
        </div>
        <div class="form-group mb-2">
            <label class="form-label me-1">Status User</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="status-user-1" value="1" name="status_user" {{
                    $users_data['is_active']==TRUE ? 'checked' : '' }}>
                <label class="form-check-label" for="status-user-1">Enable</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="status-user-2" value="0" name="status_user" {{
                    $users_data['is_active']==FALSE ? 'checked' : '' }}>
                <label class="form-check-label" for="status-user-2">Disable</label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-edit-users"><span class="bi bi-save me-1"></span>Save</button>
    </form>
    <div class="my-2 edit-users-info-data"></div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>