@extends('layouts.main')

@section('title','Management Users')

@section('head-content')
<!-- Seperate Addons CSS -->
<link rel="stylesheet" href="{{ asset('assets/vendor/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/select2/css/select2-bootstrap4.min.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="fs-4 fw-light"><span class="bi bi-people me-1"></span>Management Users
            </div>
            <div class="d-flex justify-content-between flex-wrap my-2">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target=".add-users-data">
                    <span class="bi bi-person-plus me-1"></span>Add User
                </button>
                <button class="btn btn-primary users-data-refresh">
                    <span class="bi bi-arrow-clockwise me-1"></span>Refresh
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-striped users-data" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Operator</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-content')
<!-- Seperate Addons Javascript-->
<script src="{{ asset('assets/vendor/select2/js/select2.full.min.js') }}"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        $('.users-data').DataTable({
            ajax: {
                url: "{{ route('users.getdata') }}",
                type: 'get',
                async: true,
                processing: true,
                serverSide: true,
                bDestroy: true
            },
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex'
            }, {
                data: 'name',
                name: 'name'
            }, {
                data: 'username',
                name: 'username'
            }, {
                data: 'is_operator',
                name: 'is_operator'
            }, {
                data: 'is_active',
                name: 'is_active'
            }, {
                data: 'actions',
                name: 'actions',
                orderable: true,
                searchable: true
            }, ]
        });

        $('.form-add-users').on('submit', function(event) {
            event.preventDefault();
            var form = this;
            var formdata = new FormData(this);
            $.ajax({
                type: "POST",
                url: "{{ route('users.add') }}",
                data: formdata,
                processData: false,
                contentType: false,
                async: true,
                beforeSend: function() {
                    $(".btn-add-users").html("<span class='spinner-border spinner-border-sm fa-spin me-1'></span>Saving").attr("disabled", true);
                },
                success: function(data) {
                    if (data.success == false) {
                        $(".users-info-data").html(data.messages).show();
                    } else {
                        $('.users-data').DataTable().ajax.reload();
                        $(".users-info-data").html(data.messages).show().delay(3000).fadeOut();
                        $('.add-users-data').modal('hide');
                        form.reset();
                    }
                    $(".btn-add-users").html("<span class='bi bi-save me-1'></span>Save").attr("disabled", false);
                    $('meta[name="csrf-token"]').val(data.csrftoken);
                    $('input[name=_token]').val(data.csrftoken);
                },
                error: function() {
                    $(".btn-add-users").html("<span class='bi bi-save me-1'></span>Save").attr("disabled", false);
                    swal.fire("Add Users Error", "There have problem while adding users!", "error");
                }
            });
        });

        $(".users-data").on('click', '.edit-users-data', function(event) {
            var users_data = $(event.currentTarget).attr('data-users-id');
            if (users_data === null) return;
            $.ajax({
                url: "{{ route('users.edit',['fetch' => 'show']) }}",
                type: 'POST',
                data: {id_users: users_data},
                async: true,
                beforeSend: function() {
                    $('.custom-modal-display').modal('show');
                    $('.custom-modal-content').html("<span class='spinner-border my-2'></span>").addClass("text-center");
                },
                success: function(data) {
                    $('meta[name="csrf-token"]').val(data.csrftoken);
                    $('input[name=_token]').val(data.csrftoken);
                    if(data.success == true){
                        $('.custom-modal-content').html(data.html).removeClass("text-center");
                        $('.form-edit-users').submit(function(e) {
                            e.preventDefault();
                            var form = this;
                            var formdata = new FormData(form);
                            formdata.append('id_users', users_data);
                            $.ajax({
                                url: "{{ route('users.edit',['fetch' => 'edit']) }}",
                                type: 'POST',
                                data: formdata,
                                processData: false,
                                contentType: false,
                                async: true,
                                beforeSend: function() {
                                    $(".btn-edit-users").on('.custom-modal-content').html("<span class='spinner-border spinner-border-sm me-1'></span>Saving").attr("disabled", true);
                                },
                                success: function(data) {
                                    $('meta[name="csrf-token"]').val(data.csrftoken);
                                    $('input[name=_token]').val(data.csrftoken);
                                    if (data.success == false) {
                                        $(".edit-users-info-data").html(data.messages).show();
                                    } else {
                                        $('.users-data').DataTable().ajax.reload();
                                        $(".edit-users-info-data").html(data.messages).show().delay(3000).fadeOut();
                                        $('.custom-modal-display').modal('hide');
                                        form.reset();
                                    }
                                    $(".btn-edit-users").on('.custom-modal-content').html("<span class='bi bi-save me-1'></span>Save").attr("disabled", false);
                                },
                                error: function() {
                                    $(".btn-edit-users").on('.custom-modal-content').html("<span class='bi bi-save me-1'></span>Save").attr("disabled", false);
                                }
                            });
                        });
                    }else{
                        $('.custom-modal-content').html(data.messages);
                    }
                },
                error: function(err) {
                    $('.custom-modal-content').html("<span class='bi bi-exclamation-triangle me-1'></span>There have problem while processing data").addClass("text-center");
                }
            });
            event.preventDefault();
        });

        $(".users-data").on('click', '.view-users-data', function(event) {
            var users_data = $(event.currentTarget).attr('data-users-id');
            if (users_data === null) return;
            $.ajax({
                url: "{{ route('users.view') }}",
                type: 'POST',
                data: {id_users: users_data},
                async: true,
                beforeSend: function() {
                    $('.custom-modal-display').modal('show');
                    $('.custom-modal-content').html("<span class='spinner-border my-2'></span>").addClass("text-center");
                },
                success: function(data) {
                    $('meta[name="csrf-token"]').val(data.csrftoken);
                    $('input[name=_token]').val(data.csrftoken);
                    if(data.success == true){
                        $('.custom-modal-content').html(data.html).removeClass("text-center");
                    }else{
                        $('.custom-modal-content').html(data.messages);
                    }
                },
                error: function(err) {
                    $('.custom-modal-content').html("<span class='bi bi-exclamation-triangle me-1'></span>There have problem while processing data").addClass("text-center");
                }
            });
            event.preventDefault();
        });

        $(".users-data").on('click', '.delete-users-data', function(event) {
            var users_data = $(event.currentTarget).attr('data-users-id');
            if (users_data === null) return;
            Swal.fire({
                title: 'Delete User',
                text: "This user will be erase and you cannot use that again!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('users.delete') }}",
                        type: 'POST',
                        data: {id_users: users_data},
                        async: true,
                        beforeSend: function() {
                            swal.fire({
                                title: "Deleting User",
                                text: "Please wait",
                                showConfirmButton: false,
                                allowOutsideClick: false
                            });
                            Swal.showLoading();
                        },
                        success: function(data) {
                            swal.fire({
                                icon: data.alert.icon,
                                text: data.alert.text,
                                showConfirmButton: false,
                                timer: 2500,
                                timerProgressBar: true
                            });
                            $('.users-data').DataTable().ajax.reload();
                            $('meta[name="csrf-token"]').val(data.csrftoken);
                        },
                        error: function(err) {
                            swal.fire("Delete User Failed", "There have problem while deleting user!", "error");
                        }
                    });
                }
            });
            event.preventDefault();
        });
    });

    document.querySelector(".users-data-refresh").addEventListener("click", function(e) {
        e.preventDefault();
        swal.fire({
            title: "Refresh Table",
            text: "Please wait",
            showConfirmButton: false,
            allowOutsideClick: false,
            timer: 800,
            timerProgressBar: true
        });
        Swal.showLoading();
        $('.users-data').DataTable().ajax.reload();
    });
</script>
@endsection

@section('modal-content')
<div class="modal fade add-users-data" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel"><span class="bi bi-person-plus me-1"></span>Add User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-add-users">
                    <div class="form-group mb-2">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name_user" placeholder="Name">
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username_user" placeholder="Username">
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
                            <input class="form-check-input" type="radio" id="operator-user-1" value="1"
                                name="operator_user">
                            <label class="form-check-label" for="operator-user-1">Enable</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="operator-user-2" value="0"
                                name="operator_user">
                            <label class="form-check-label" for="operator-user-2">Disable</label>
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label me-1">Status User</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="status-user-1" value="1"
                                name="status_user">
                            <label class="form-check-label" for="status-user-1">Enable</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="status-user-2" value="0"
                                name="status_user">
                            <label class="form-check-label" for="status-user-2">Disable</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-add-users"><span
                            class="bi bi-save me-1"></span>Save</button>
                </form>
                <div class="my-2 users-info-data"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection