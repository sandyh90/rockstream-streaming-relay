@extends('layouts.main')

@section('title','Premiere Video')

@section('head-content')
<!-- Seperate Addons CSS -->
<link rel="stylesheet" href="{{ asset('assets/vendor/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/select2/css/select2-bootstrap4.min.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between flex-wrap my-2">
                <div class="fs-4 fw-light"><span class="bi bi-play-btn me-1"></span>Premiere Video
                </div>
                <div role="button" data-bs-toggle="modal" data-bs-target=".modal-countdown-video-credit">
                    <span class="bi bi-info-circle me-1"></span>Additional Credit
                </div>
            </div>
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <span class="bi bi-exclamation-triangle-fill flex-shrink-0 fs-2 me-2"></span>
                <div>
                    [Beta] This feature is still under development and maybe not working properly or at all. Please
                    report any bugs you find.
                </div>
            </div>
            <nav>
                <div class="nav nav-tabs" id="nav-interfaces-settings-tab" role="tablist">
                    <button class="nav-link active" id="nav-premiere-panel-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-premiere-panel" type="button" role="tab" aria-controls="nav-premiere-panel"
                        aria-selected="true"><span class="bi bi-play-btn me-1"></span>Premiere Panel</button>
                    <button class="nav-link" id="nav-premiere-queue-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-premiere-queue" type="button" role="tab"
                        aria-controls="nav-premiere-queue"><span class="bi bi-collection-play me-1"></span>Premiere
                        Queue</button>
                </div>
            </nav>
            <div class="tab-content mt-2" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-premiere-panel" role="tabpanel"
                    aria-labelledby="nav-premiere-panel-tab">
                    <div class="fw-light fs-5">Premiere Panel</div>
                    <hr>
                    <div class="d-flex justify-content-between flex-wrap my-2">
                        <div class="button-group premiere-options-btn">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                data-bs-target=".add-premiere-video">
                                <span class="bi bi-film me-1"></span>Add Premiere Video
                            </button>
                            <button class="btn btn-danger launch-daemon-premiere-video">
                                <span class="bi bi-cpu me-1"></span>Launch Daemon
                            </button>
                        </div>
                        <button class="btn btn-primary premiere-video-data-refresh">
                            <span class="bi bi-arrow-clockwise me-1"></span>Refresh
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped premiere-video-data" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name Video</th>
                                    <th>Local Path Video</th>
                                    <th>Premiere</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-premiere-queue" role="tabpanel"
                    aria-labelledby="nav-premiere-queue-tab">
                    <div class="fw-light fs-5">Premiere Queue</div>
                    <hr>
                    <div class="d-flex justify-content-between flex-wrap my-2">
                        <button class="btn btn-primary premiere-queue-data-refresh">
                            <span class="bi bi-arrow-clockwise me-1"></span>Refresh
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped premiere-queue-data" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Queue</th>
                                    <th>Running At</th>
                                    <th>Scheduled At</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
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
        $('input[name="path_video"]').on('keyup keypress change', function(e) {
            var value = $(this).val();
            if (value.indexOf('"') != -1) {
                $(this).val(value.replace(/\"/g, ""));
            }else if (value.indexOf('\'') != -1) {
                $(this).val(value.replace(/\'/g, ""));
            }
        });

        $('.premiere-video-data').DataTable({
            ajax: {
                url: "{{ route('premiere.getdata') }}",
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
                data: 'title_video',
                name: 'title_video'
            }, {
                data: 'video_path',
                name: 'video_path'
            }, {
                data: 'is_premiere',
                name: 'is_premiere'
            }, {
                data: 'active_premiere_video',
                name: 'active_premiere_video'
            }, {
                data: 'actions',
                name: 'actions',
                orderable: true,
                searchable: true
            }, ]
        });

        $('.form-add-premiere-video').on('submit', function(event) {
            event.preventDefault();
            var form = this;
            var formdata = new FormData(this);
            $.ajax({
                type: "POST",
                url: "{{ route('premiere.add') }}",
                data: formdata,
                processData: false,
                contentType: false,
                async: true,
                beforeSend: function() {
                    $(".btn-add-premiere-video").html("<span class='spinner-border spinner-border-sm fa-spin me-1'></span>Saving").attr("disabled", true);
                },
                success: function(data) {
                    if (data.success == false) {
                        if(data.isForm == true){
                            msgalert(".premiere-video-info-data", data.messages);
                        }else{
                            if ($(".premiere-video-info-data").hasClass("alert alert-danger")) {
                                $(".premiere-video-info-data").removeClass("alert alert-danger");
                            }
                            $(".premiere-video-info-data").html(data.messages).show();
                        }
                    } else {
                        $('.premiere-video-data').DataTable().ajax.reload();
                        if ($(".premiere-video-info-data").hasClass("alert alert-danger")) {
                            $(".premiere-video-info-data").removeClass("alert alert-danger");
                        }
                        $(".premiere-video-info-data").html(data.messages).show().delay(3000).fadeOut();
                        $('.add-premiere-video').modal('hide');
                        form.reset();
                    }
                    $(".btn-add-premiere-video").html("<span class='bi bi-save me-1'></span>Save").attr("disabled", false);
                    $('meta[name="csrf-token"]').val(data.csrftoken);
                    $('input[name=_token]').val(data.csrftoken);
                },
                error: function() {
                    $(".btn-add-premiere-video").html("<span class='bi bi-save me-1'></span>Save").attr("disabled", false);
                    swal.fire("Add Premiere Video Error", "There have problem while adding premiere video!", "error");
                }
            });
        });

        $(".premiere-video-data").on('click', '.edit-premiere-video', function(event) {
            var premiere_video_data = $(event.currentTarget).attr('data-premiere-video-id');
            if (premiere_video_data === null) return;
            $.ajax({
                url: "{{ route('premiere.edit',['fetch' => 'show']) }}",
                type: 'POST',
                data: {id_premiere_video: premiere_video_data},
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
                        $('.form-edit-premiere-video').submit(function(e) {
                            e.preventDefault();
                            var form = this;
                            var formdata = new FormData(form);
                            formdata.append('id_premiere_video', premiere_video_data);
                            $.ajax({
                                url: "{{ route('premiere.edit',['fetch' => 'edit']) }}",
                                type: 'POST',
                                data: formdata,
                                processData: false,
                                contentType: false,
                                async: true,
                                beforeSend: function() {
                                    $(".btn-edit-premiere-video").on('.custom-modal-content').html("<span class='spinner-border spinner-border-sm me-1'></span>Saving").attr("disabled", true);
                                },
                                success: function(data) {
                                    $('meta[name="csrf-token"]').val(data.csrftoken);
                                    $('input[name=_token]').val(data.csrftoken);
                                    if (data.success == false) {
                                        if(data.isForm == true){
                                            msgalert(".edit-premiere-video-info-data", data.messages);
                                        }else{
                                            if ($(".edit-premiere-video-info-data").hasClass("alert alert-danger")) {
                                                $(".edit-premiere-video-info-data").removeClass("alert alert-danger");
                                            }
                                            $(".edit-premiere-video-info-data").html(data.messages).show();
                                        }
                                    } else {
                                        $('.premiere-video-data').DataTable().ajax.reload();
                                        if ($(".edit-premiere-video-info-data").hasClass("alert alert-danger")) {
                                            $(".edit-premiere-video-info-data").removeClass("alert alert-danger");
                                        }
                                        $(".edit-premiere-video-info-data").html(data.messages).show().delay(3000).fadeOut();
                                        $('.custom-modal-display').modal('hide');
                                        form.reset();
                                    }
                                    $(".btn-edit-premiere-video").on('.custom-modal-content').html("<span class='bi bi-save me-1'></span>Save").attr("disabled", false);
                                },
                                error: function() {
                                    $(".btn-edit-premiere-video").on('.custom-modal-content').html("<span class='bi bi-save me-1'></span>Save").attr("disabled", false);
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

        $(".premiere-video-data").on('click', '.start-premiere-video', function(event) {
            var premiere_video_data = $(event.currentTarget).attr('data-premiere-video-id');
            if (premiere_video_data === null) return;
            $.ajax({
                url: "{{ route('premiere.start_play',['fetch' => 'show']) }}",
                type: 'POST',
                data: {id_premiere_video: premiere_video_data},
                async: true,
                beforeSend: function() {
                    $('.custom-modal-display').modal('show');
                    $('.custom-modal-content').html("<span class='spinner-border my-2'></span>").addClass("text-center");
                },
                success: function(data) {
                    $('meta[name="csrf-token"]').val(data.csrftoken);
                    $('input[name=_token]').val(data.csrftoken);
                    if(data.success == true){
                        $('.custom-modal-display').children(".modal-dialog").addClass("modal-xl");
                        $('.custom-modal-content').html(data.html).removeClass("text-center");
                        $('.form-start-premiere-video').submit(function(e) {
                            e.preventDefault();
                            var form = this;
                            var formdata = new FormData(form);
                            formdata.append('id_premiere_video', premiere_video_data);
                            $.ajax({
                                url: "{{ route('premiere.start_play',['fetch' => 'start']) }}",
                                type: 'POST',
                                data: formdata,
                                processData: false,
                                contentType: false,
                                async: true,
                                beforeSend: function() {
                                    $(".btn-start-premiere-video").on('.custom-modal-content').html("<span class='spinner-border spinner-border-sm me-1'></span>Starting Premiere").attr("disabled", true);
                                },
                                success: function(data) {
                                    $('meta[name="csrf-token"]').val(data.csrftoken);
                                    $('input[name=_token]').val(data.csrftoken);
                                    if (data.success == false) {
                                        if(data.isForm == true){
                                            msgalert(".start-premiere-video-info-data", data.messages);
                                        }else{
                                            if ($(".start-premiere-video-info-data").hasClass("alert alert-danger")) {
                                                $(".start-premiere-video-info-data").removeClass("alert alert-danger");
                                            }
                                            $(".start-premiere-video-info-data").html(data.messages).show();
                                        }
                                    } else {
                                        $('.premiere-video-data').DataTable().ajax.reload();
                                        $('.premiere-queue-data').DataTable().ajax.reload();
                                        if ($(".start-premiere-video-info-data").hasClass("alert alert-danger")) {
                                            $(".start-premiere-video-info-data").removeClass("alert alert-danger");
                                        }
                                        $(".start-premiere-video-info-data").html(data.messages).show().delay(3000).fadeOut();
                                        $('.custom-modal-display').modal('hide');
                                        form.reset();
                                    }
                                    $(".btn-start-premiere-video").on('.custom-modal-content').html("<span class='bi bi-broadcast me-1'></span>Start Premiere").attr("disabled", false);
                                },
                                error: function() {
                                    $(".btn-start-premiere-video").on('.custom-modal-content').html("<span class='bi bi-broadcast me-1'></span>Start Premiere").attr("disabled", false);
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

        $(".premiere-video-data").on('click', '.view-premiere-video', function(event) {
            var premiere_video_data = $(event.currentTarget).attr('data-premiere-video-id');
            if (premiere_video_data === null) return;
            $.ajax({
                url: "{{ route('premiere.view') }}",
                type: 'POST',
                data: {id_premiere_video: premiere_video_data},
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

        $(".premiere-options-btn").on('click', '.launch-daemon-premiere-video', function(event) {
            $.ajax({
                url: "{{ route('premiere.launch_daemon') }}",
                type: 'OPTIONS',
                async: true,
                beforeSend: function() {
                    $(".launch-daemon-premiere-video").on('.premiere-options-btn').html("<span class='spinner-border spinner-border-sm me-1'></span>Launching Daemon").attr("disabled", true);
                   swal.fire({
                        title: "Launching Daemon For Premiere Video",
                        text: "Please wait",
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });
                    Swal.showLoading();
                },
                success: function(data) {
                    $(".launch-daemon-premiere-video").on('.premiere-options-btn').html("<span class='bi bi-cpu me-1'></span>Launch Daemon").attr("disabled", false);
                    swal.fire({
                        icon: data.alert.icon,
                        title: data.alert.title,
                        text: data.alert.text,
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                    $('meta[name="csrf-token"]').val(data.csrftoken);
                    $('input[name=_token]').val(data.csrftoken);
                },
                error: function(err) {
                    $(".launch-daemon-premiere-video").on('.premiere-options-btn').html("<span class='bi bi-cpu me-1'></span>Launch Daemon").attr("disabled", false);
                    swal.fire("Launching Daemon Premiere Video Failed", "There have problem while launching daemon premiere video!", "error");
                }
            });
            event.preventDefault();
        });

        $(".premiere-video-data").on('click', '.delete-premiere-video', function(event) {
            var premiere_video_data = $(event.currentTarget).attr('data-premiere-video-id');
            if (premiere_video_data === null) return;
            Swal.fire({
                title: 'Delete Premiere Video',
                text: "This premiere video will be erase and you cannot use that again!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('premiere.delete') }}",
                        type: 'POST',
                        data: {id_premiere_video: premiere_video_data},
                        async: true,
                        beforeSend: function() {
                            swal.fire({
                                title: "Deleting Premiere Video",
                                text: "Please wait",
                                showConfirmButton: false,
                                allowOutsideClick: false
                            });
                            Swal.showLoading();
                        },
                        success: function(data) {
                            swal.fire({
                                icon: data.alert.icon,
                                title: data.alert.title,
                                showConfirmButton: false,
                                timer: 1500,
                                timerProgressBar: true
                            });
                            $('.premiere-video-data').DataTable().ajax.reload();
                            $('meta[name="csrf-token"]').val(data.csrftoken);
                        },
                        error: function(err) {
                            swal.fire("Delete Premiere Video Failed", "There have problem while deleting premiere video!", "error");
                        }
                    });
                }
            });
            event.preventDefault();
        });

        $(".premiere-video-data").on('click', '.force-status-premiere-video', function(event) {
            var premiere_video_data = $(event.currentTarget).attr('data-premiere-video-id');
            if (premiere_video_data === null) return;
            Swal.fire({
                title: 'Force Status Premiere Video',
                text: "This premiere video will be force to offline, It may help you if the status videos are stuck in the premiere state!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Force'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('premiere.force_status') }}",
                        type: 'POST',
                        data: {id_premiere_video: premiere_video_data},
                        async: true,
                        beforeSend: function() {
                            swal.fire({
                                title: "Forcing Status Premiere Video",
                                text: "Please wait",
                                showConfirmButton: false,
                                allowOutsideClick: false
                            });
                            Swal.showLoading();
                        },
                        success: function(data) {
                            swal.fire({
                                icon: data.alert.icon,
                                title: data.alert.title,
                                showConfirmButton: false,
                                timer: 1500,
                                timerProgressBar: true
                            });
                            $('.premiere-video-data').DataTable().ajax.reload();
                            $('meta[name="csrf-token"]').val(data.csrftoken);
                        },
                        error: function(err) {
                            swal.fire("Force Status Premiere Video Failed", "There have problem while forcing status premiere video!", "error");
                        }
                    });
                }
            });
            event.preventDefault();
        });

        $('.premiere-queue-data').DataTable({
            ajax: {
                url: "{{ route('premiere.getdata.queue') }}",
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
                data: 'queue_name',
                name: 'queue_name',
            }, {
                data: 'running_at',
                name: 'running_at',
            }, {
                data: 'schedule_at',
                name: 'schedule_at',
            }, {
                data: 'created_at',
                name: 'created_at',
            }, {
                data: 'actions',
                name: 'actions',
                orderable: true,
                searchable: true
            }, ]
        });

        $(".premiere-queue-data").on('click', '.view-premiere-queue', function(event) {
            var premiere_queue_data = $(event.currentTarget).attr('data-premiere-queue-id');
            if (premiere_queue_data === null) return;
            $.ajax({
                url: "{{ route('premiere.view.queue') }}",
                type: 'POST',
                data: {id_premiere_queue: premiere_queue_data},
                async: true,
                beforeSend: function() {
                    $('.custom-modal-display').modal('show');
                    $('.custom-modal-content').html("<span class='spinner-border my-2'></span>").addClass("text-center");
                },
                success: function(data) {
                    $('meta[name="csrf-token"]').val(data.csrftoken);
                    $('input[name=_token]').val(data.csrftoken);
                    if(data.success == true){
                        $('.custom-modal-display').children(".modal-dialog").addClass("modal-lg");
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

        $(".premiere-queue-data").on('click', '.edit-premiere-queue', function(event) {
            var premiere_queue_data = $(event.currentTarget).attr('data-premiere-queue-id');
            if (premiere_queue_data === null) return;
            $.ajax({
                url: "{{ route('premiere.edit.queue',['fetch' => 'show']) }}",
                type: 'POST',
                data: {id_premiere_queue: premiere_queue_data},
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
                        $('.form-edit-premiere-queue').submit(function(e) {
                            e.preventDefault();
                            var form = this;
                            var formdata = new FormData(form);
                            formdata.append('id_premiere_queue', premiere_queue_data);
                            $.ajax({
                                url: "{{ route('premiere.edit.queue',['fetch' => 'edit']) }}",
                                type: 'POST',
                                data: formdata,
                                processData: false,
                                contentType: false,
                                async: true,
                                beforeSend: function() {
                                    $(".btn-edit-premiere-queue").on('.custom-modal-content').html("<span class='spinner-border spinner-border-sm me-1'></span>Saving").attr("disabled", true);
                                },
                                success: function(data) {
                                    $('meta[name="csrf-token"]').val(data.csrftoken);
                                    $('input[name=_token]').val(data.csrftoken);
                                    if (data.success == false) {
                                        if(data.isForm == true){
                                            msgalert(".edit-premiere-queue-info-data", data.messages);
                                        }else{
                                            if ($(".edit-premiere-queue-info-data").hasClass("alert alert-danger")) {
                                                $(".edit-premiere-queue-info-data").removeClass("alert alert-danger");
                                            }
                                            $(".edit-premiere-queue-info-data").html(data.messages).show();
                                        }
                                    } else {
                                        $('.premiere-queue-data').DataTable().ajax.reload();
                                        if ($(".edit-premiere-queue-info-data").hasClass("alert alert-danger")) {
                                            $(".edit-premiere-queue-info-data").removeClass("alert alert-danger");
                                        }
                                        $(".edit-premiere-queue-info-data").html(data.messages).show().delay(3000).fadeOut();
                                        $('.custom-modal-display').modal('hide');
                                        form.reset();
                                    }
                                    $(".btn-edit-premiere-queue").on('.custom-modal-content').html("<span class='bi bi-save me-1'></span>Save").attr("disabled", false);
                                },
                                error: function() {
                                    $(".btn-edit-premiere-queue").on('.custom-modal-content').html("<span class='bi bi-save me-1'></span>Save").attr("disabled", false);
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

        $(".premiere-queue-data").on('click', '.delete-premiere-queue', function(event) {
            var premiere_queue_data = $(event.currentTarget).attr('data-premiere-queue-id');
            if (premiere_queue_data === null) return;
            Swal.fire({
                title: 'Delete Premiere Queue',
                text: "This premiere queue will be erase!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('premiere.delete.queue') }}",
                        type: 'POST',
                        data: {id_premiere_queue: premiere_queue_data},
                        async: true,
                        beforeSend: function() {
                            swal.fire({
                                title: "Deleting Premiere Queue",
                                text: "Please wait",
                                showConfirmButton: false,
                                allowOutsideClick: false
                            });
                            Swal.showLoading();
                        },
                        success: function(data) {
                            swal.fire({
                                icon: data.alert.icon,
                                title: data.alert.title,
                                showConfirmButton: false,
                                timer: 1500,
                                timerProgressBar: true
                            });
                            $('.premiere-queue-data').DataTable().ajax.reload();
                            $('meta[name="csrf-token"]').val(data.csrftoken);
                        },
                        error: function(err) {
                            swal.fire("Delete Premiere Queue Failed", "There have problem while deleting premiere job!", "error");
                        }
                    });
                }
            });
            event.preventDefault();
        });
    });

    document.querySelector(".premiere-video-data-refresh").addEventListener("click", function(e) {
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
        $('.premiere-video-data').DataTable().ajax.reload();
    });

    document.querySelector(".premiere-queue-data-refresh").addEventListener("click", function(e) {
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
        $('.premiere-queue-data').DataTable().ajax.reload();
    });

    function msgalert(sector, msg) {
        $(sector).show();
        $(sector).find('ul').children().remove();
        $(sector).html('<ul></ul>').addClass("alert alert-danger");
        $.each(msg, function(key, value) {
            $(sector).find("ul").append('<li>' + value + '</li>');
        });
    }
</script>
@endsection

@section('modal-content')
<div class="modal fade add-premiere-video" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel"><span class="bi bi-play-btn me-1"></span>Add
                    Premiere
                    Video
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-add-premiere-video">
                    <div class="form-group mb-2">
                        <label class="form-label">Name Premiere Video</label>
                        <input type="text" class="form-control" name="name_video" placeholder="Name Video">
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label">Local Path Video</label>
                        <input type="text" class="form-control" name="path_video" placeholder="Path Video">
                        <small class="form-text text-muted">*For path video please remove quote (") if
                            present.</small>
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label me-1">Status Premiere Video</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="status-video-1" value="1"
                                name="status_video">
                            <label class="form-check-label" for="status-video-1">Enable</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="status-video-2" value="0"
                                name="status_video">
                            <label class="form-check-label" for="status-video-2">Disable</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-add-premiere-video"><span
                            class="bi bi-save me-1"></span>Save</button>
                </form>
                <div class="my-2 premiere-video-info-data"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-countdown-video-credit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    role="dialog">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content rounded-6 shadow">
            <div class="modal-body">
                @include('layouts.info_layouts.credit_countdown_video_assets')
                <button type="button" class="btn btn-primary btn-dismiss-countdown-video-credit-modal mt-2 w-100"
                    data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
@endsection