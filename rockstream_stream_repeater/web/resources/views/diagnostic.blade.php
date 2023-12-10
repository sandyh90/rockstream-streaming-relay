@extends('layouts.main')

@section('title','Diagnostic')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="fs-4 fw-light my-2"><span class="bi bi-wrench-adjustable-circle me-1"></span>Diagnostic
            </div>
            <nav>
                <div class="nav nav-tabs" id="nav-diagnostic-tab" role="tablist">
                    <button class="nav-link active" id="nav-failed-queue-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-failed-queue" type="button" role="tab"
                        aria-controls="nav-failed-queue"><span class="bi bi-terminal-x me-1"></span>List Failed
                        Queue</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-failed-queue" role="tabpanel"
                    aria-labelledby="nav-failed-queue-tab" tabindex="0">
                    <h4 class="fw-light my-2">List Failed Queue</h4>
                    <div class="d-flex justify-content-between flex-wrap my-2 queue-failed-btn-list">
                        <button class="btn btn-primary failed-queue-data-refresh">
                            <span class="bi bi-arrow-clockwise me-1"></span>Refresh
                        </button>
                        <button class="btn btn-danger failed-queue-data-clear-all">
                            <span class="bi bi-trash me-1"></span>Clear All
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped failed-queue-data" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Jobs</th>
                                    <th>Queue</th>
                                    <th>Failed At</th>
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
<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('.failed-queue-data').DataTable({
            ajax: {
                url: "{{ route('diagnostic.getdata.failed_queue') }}",
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
                data: 'jobs_name',
                name: 'jobs_name',
            }, {
                data: 'queue_name',
                name: 'queue_name',
            }, {
                data: 'failed_at',
                name: 'failed_at',
            }, {
                data: 'actions',
                name: 'actions',
                orderable: true,
                searchable: true
            }, ]
        });

        $(".failed-queue-data").on('click', '.view-failed-queue', function(event) {
            var failed_queue_data = $(event.currentTarget).attr('data-failed-queue-id');
            if (failed_queue_data === null) return;
            $.ajax({
                url: "{{ route('diagnostic.view.failed_queue') }}",
                type: 'POST',
                data: {id_failed_queue: failed_queue_data},
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

        $(".failed-queue-data").on('click', '.delete-failed-queue', function(event) {
            var failed_queue_data = $(event.currentTarget).attr('data-failed-queue-id');
            if (failed_queue_data === null) return;
            Swal.fire({
                title: 'Delete Failed Job',
                text: "This failed queue will be erase and you cannot use that again!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('diagnostic.delete.failed_queue') }}",
                        type: 'POST',
                        data: {id_failed_queue: failed_queue_data},
                        async: true,
                        beforeSend: function() {
                            swal.fire({
                                title: "Deleting Failed Job",
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
                                timer: 1500,
                                timerProgressBar: true
                            });
                            $('.failed-queue-data').DataTable().ajax.reload();
                            $('meta[name="csrf-token"]').val(data.csrftoken);
                        },
                        error: function(err) {
                            swal.fire("Delete Failed Job Failed", "There have problem while deleting failed job!", "error");
                        }
                    });
                }
            });
            event.preventDefault();
        });

        $(".failed-queue-data").on('click', '.retry-failed-queue', function(event) {
            var failed_queue_data = $(event.currentTarget).attr('data-failed-queue-id');
            if (failed_queue_data === null) return;
            Swal.fire({
                title: 'Retry Failed Job',
                text: "This failed queue will be retry to queue again!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Retry'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('diagnostic.retry.failed_queue') }}",
                        type: 'POST',
                        data: {id_failed_queue: failed_queue_data},
                        async: true,
                        beforeSend: function() {
                            swal.fire({
                                title: "Retrying Failed Job",
                                text: "Please wait",
                                showConfirmButton: false,
                                allowOutsideClick: false
                            });
                            Swal.showLoading();
                        },
                        success: function(data) {
                            swal.fire({
                                icon: data.alert.type,
                                title: data.alert.title,
                                text: data.alert.text,
                                showConfirmButton: false,
                                timer: data.alert.timer,
                                timerProgressBar: true
                            });
                            $('#failedQueueData').DataTable().ajax.reload();
                            $('meta[name="csrf-token"]').val(data.csrftoken);
                        },
                        error: function(err) {
                            swal.fire("Retry Failed Job Failed", "There have problem while retrying failed job!", "error");
                        }
                    });
                }
            });
            event.preventDefault();
        });

        $(".queue-failed-btn-list").on('click', '.failed-queue-data-clear-all', function(event) {
            Swal.fire({
                title: 'Clear All Failed Job',
                text: "This all failed queue will be erase!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Clear'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('diagnostic.clear.failed_queue') }}",
                        type: 'POST',
                        async: true,
                        beforeSend: function() {
                            swal.fire({
                                title: "Deleting Failed Job",
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
                                timer: 1500,
                                timerProgressBar: true
                            });
                            $('.failed-queue-data').DataTable().ajax.reload();
                            $('meta[name="csrf-token"]').val(data.csrftoken);
                        },
                        error: function(err) {
                            swal.fire("Clear All Failed Job Failed", "There have problem while clearing failed job!", "error");
                        }
                    });
                }
            });
            event.preventDefault();
        });
    });

    document.querySelector(".failed-queue-data-refresh").addEventListener("click", function(e) {
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
        $('.failed-queue-data').DataTable().ajax.reload();
    });
</script>
@endsection