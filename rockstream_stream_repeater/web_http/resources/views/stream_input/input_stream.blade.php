@extends('layouts.main')

@section('title','Input Stream')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="fs-4 fw-light my-2"><span class="bi bi-hdmi me-1"></span>Input Stream
            </div>
            <div class="d-flex justify-content-between flex-wrap my-2">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target=".add-input-stream">
                    <span class="bi bi-hdmi me-1"></span>Add Input Stream
                </button>
                <button class="btn btn-primary input-stream-data-refresh">
                    <span class="bi bi-arrow-clockwise me-1"></span>Refresh
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-striped input-stream-data" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name Input</th>
                            <th>Destination List</th>
                            <th>Live</th>
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
<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('.input-stream-data').DataTable({
            ajax: {
                url: "{{ route('stream.getdata') }}",
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
                data: 'name_input',
                name: 'name_input'
            }, {
                data: 'name_dest',
                name: 'name_dest'
            }, {
                data: 'is_live',
                name: 'is_live'
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

        $('.form-add-input-stream').on('submit', function(event) {
            event.preventDefault();
            var form = this;
            var formdata = new FormData(this);
            $.ajax({
                type: "POST",
                url: "{{ route('stream.add') }}",
                data: formdata,
                processData: false,
                contentType: false,
                async: true,
                beforeSend: function() {
                    $(".btn-add-input-stream").html("<span class='spinner-border spinner-border-sm fa-spin me-1'></span>Saving").attr("disabled", true);
                },
                success: function(data) {
                    if (data.success == false) {
                        msgalert(".input-stream-info-data", data.messages);
                    } else {
                        $('.input-stream-data').DataTable().ajax.reload();
                        if ($(".input-stream-info-data").hasClass("alert alert-danger")) {
                            $(".input-stream-info-data").removeClass("alert alert-danger");
                        }
                        $(".input-stream-info-data").html(data.messages).show().delay(3000).fadeOut();
                        $('.add-input-stream').modal('hide');
                        form.reset();
                    }
                    $(".btn-add-input-stream").html("<span class='material-icons me-1'>save</span>Save").attr("disabled", false);
                    $('meta[name="csrf-token"').val(data.csrftoken);
                },
                error: function() {
                    $(".btn-add-input-stream").html("<span class='material-icons me-1'>save</span>Save").attr("disabled", false);
                    swal.fire("Add Input Stream Error", "There have problem while adding input stream!", "error");
                }
            });
        });

        $(".input-stream-data").on('click', '.regen-input-stream-key', function(event) {
            var input_stream_data = $(event.currentTarget).attr('data-input-stream-id');
            if (input_stream_data === null) return;
            $.ajax({
                url: "{{ route('stream.regenstreamkey') }}",
                type: 'POST',
                data: {id_input_stream: input_stream_data},
                async: true,
                beforeSend: function() {
                    swal.fire({
                        title: "Regenerating Stream Key",
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
                    $('.input-stream-data').DataTable().ajax.reload();
                    $('meta[name="csrf-token"').val(data.csrftoken);
                },
                error: function(err) {
                    swal.fire("Regenerate Stream Key Failed", "There have problem while regenerate stream key!", "error");
                }
            });
            event.preventDefault();
        });

        $(".input-stream-data").on('click', '.view-input-stream', function(event) {
            var input_stream_data = $(event.currentTarget).attr('data-input-stream-id');
            if (input_stream_data === null) return;
            $.ajax({
                url: "{{ route('stream.view') }}",
                type: 'POST',
                data: {id_input_stream: input_stream_data},
                async: true,
                beforeSend: function() {
                    $('.custom-modal-display').modal('show');
                    $('.custom-modal-content').html("<span class='spinner-border my-2'></span>").addClass("text-center");
                },
                success: function(data) {
                    $('meta[name="csrf-token"').val(data.csrftoken);
                    $('input[name=_token]').val(data.csrftoken);
                    if(data.success == true){
                        $('.custom-modal-content').html(data.html).removeClass("text-center");
                    }else{
                        $('.custom-modal-content').html(data.messages);
                    }
                },
                error: function(err) {
                    $('.custom-modal-content').html("<span class='material-icons me-1'>warning</span>There have problem while processing data").addClass("text-center");
                }
            });
            event.preventDefault();
        });

        $(".input-stream-data").on('click', '.delete-input-stream', function(event) {
            var input_stream_data = $(event.currentTarget).attr('data-input-stream-id');
            if (input_stream_data === null) return;
            Swal.fire({
                title: 'Delete Input Stream',
                text: "This input stream will be erase and you cannot use that again!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('stream.delete') }}",
                        type: 'POST',
                        data: {id_input_stream: input_stream_data},
                        async: true,
                        beforeSend: function() {
                            swal.fire({
                                title: "Deleting Input Stream",
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
                            $('.input-stream-data').DataTable().ajax.reload();
                            $('meta[name="csrf-token"').val(data.csrftoken);
                        },
                        error: function(err) {
                            swal.fire("Delete Input Stream Failed", "There have problem while deleting input stream!", "error");
                        }
                    });
                }
            });
            event.preventDefault();
        });
    });

    document.querySelector(".input-stream-data-refresh").addEventListener("click", function(e) {
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
        $('.input-stream-data').DataTable().ajax.reload();
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
<div class="modal fade add-input-stream" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel"><span class="bi bi-hdmi me-1"></span>Add Input
                    Stream
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-add-input-stream">
                    <div class="form-group mb-2">
                        <label class="form-label">Name Input</label>
                        <input type="text" class="form-control" name="name_input" placeholder="Name Input">
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label me-1">Status Input</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="status-input-1" value="1"
                                name="status_input">
                            <label class="form-check-label" for="status-input-1">Enable</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="status-input-2" value="0"
                                name="status_input">
                            <label class="form-check-label" for="status-input-2">Disable</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-add-input-stream"><span
                            class="material-icons me-1">save</span>Save</button>
                </form>
                <div class="my-2 input-stream-info-data"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection