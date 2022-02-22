@extends('layouts.main')

@section('title','Edit Input Stream')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between flex-wrap my-2">
        <a class="btn btn-primary" href="{{ route('stream.home') }}"><span class="bi bi-arrow-left me-1"></span>Back</a>
        <h4 class="fw-light text-truncate">{{ \Str::words($stream_input->name_input, 8) }}</h4>
        {!! $stream_input->is_live == TRUE ? '<div class="text-success flash-text-item"><span
                class="material-icons me-1">sensors</span>Live</div>' : '<div class="text-danger"><span
                class="material-icons me-1">sensors_off</span>Not Live</div>' !!}
    </div>
    <div class="row">
        <div class="col-xl-4">
            <div class="card card-body mb-2">
                <div class="fs-4 fw-light my-2"><span class="bi bi-hdmi me-1"></span>Edit Input
                    Stream
                </div>
                <form class="form-edit-input-stream">
                    <div class="form-group mb-2">
                        <label class="form-label">Name Input</label>
                        <input type="text" class="form-control" name="name_input" placeholder="Name Input"
                            value="{{ $stream_input->name_input }}">
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label me-1">Status Input</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="status-input-1" value="1"
                                name="status_input" {{ $stream_input->active_input_stream == TRUE ? 'checked' :
                            '' }}>
                            <label class="form-check-label" for="status-input-1">Enable</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="status-input-2" value="0"
                                name="status_input" {{ $stream_input->active_input_stream == FALSE ? 'checked' :
                            '' }}>
                            <label class="form-check-label" for="status-input-2">Disable</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-edit-input-stream"><span
                            class="bi bi-save me-1"></span>Save</button>
                </form>
                <div class="my-2 edit-input-stream-info-data"></div>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <div class="fs-4 fw-light my-2"><span class="bi bi-broadcast me-1"></span>Output
                        Destination
                    </div>
                    <div class="d-flex justify-content-between flex-wrap my-2">
                        @if ($stream_input->is_live != TRUE)
                        <button type="button" class="btn btn-success" data-bs-toggle="modal"
                            data-bs-target=".add-output-dest">
                            <span class="bi bi-broadcast me-1"></span>Add Output
                        </button>
                        @else
                        <button type="button" class="btn btn-danger" disabled>
                            <span class="material-icons me-1">block</span>Add Output
                        </button>
                        @endif
                        <button class="btn btn-primary output-dest-data-refresh">
                            <span class="bi bi-arrow-clockwise me-1"></span>Refresh
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped output-dest-data" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name Destination</th>
                                    <th>Platform</th>
                                    <th>Status</th>
                                    <th>Action</th>
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
        $('.output-dest-data').DataTable({
            ajax: {
                url: "{{ route('outputdest.getdata') }}",
                type: 'get',
                data: {
                    id_input_stream: "{{ $stream_input->identifier_stream }}"
                },
                async: true,
                processing: true,
                serverSide: true,
                bDestroy: true
            },
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex'
            }, {
                data: 'name_stream_dest',
                name: 'name_stream_dest'
            }, {
                data: 'platform_dest',
                name: 'platform_dest'
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

        $('.form-edit-input-stream').on('submit', function(event) {
            event.preventDefault();
            var form = this;
            var formdata = new FormData(this);
            formdata.append('id_input_stream', '{{ $stream_input->identifier_stream }}');
            $.ajax({
                type: "POST",
                url: "{{ route('stream.edit') }}",
                data: formdata,
                processData: false,
                contentType: false,
                async: true,
                beforeSend: function() {
                    $(".btn-edit-input-stream").html("<span class='spinner-border spinner-border-sm me-1'></span>Saving").attr("disabled", true);
                },
                success: function(data) {
                    if (data.success == false) {
                        if(data.isForm == true){
                            msgalert(".edit-input-stream-info-data", data.messages);
                        }else{
                            $(".edit-input-stream-info-data").html(data.messages).show().delay(3000).fadeOut();
                        }
                    } else {
                        if ($(".edit-input-stream-info-data").hasClass("alert alert-danger")) {
                            $(".edit-input-stream-info-data").removeClass("alert alert-danger");
                        }
                        $(".edit-input-stream-info-data").html(data.messages).show().delay(3000).fadeOut();
                    }
                    $(".btn-edit-input-stream").html("<span class='bi bi-save me-1'></span>Save").attr("disabled", false);
                    $('meta[name="csrf-token"').val(data.csrftoken);
                },
                error: function() {
                    $(".btn-edit-input-stream").html("<span class='bi bi-save me-1'></span>Save").attr("disabled", false);
                    swal.fire("Edit Input Stream Error", "There have problem while editing input stream!", "error");
                }
            });
        });
    });
</script>
@if ($stream_input->is_live != TRUE)
<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('.form-add-output-dest').on('submit', function(event) {
            event.preventDefault();
            var form = this;
            var formdata = new FormData(this);
            formdata.append('input_stream_id', '{{ $stream_input->id }}');
            $.ajax({
                type: "POST",
                url: "{{ route('outputdest.add') }}",
                data: formdata,
                processData: false,
                contentType: false,
                async: true,
                beforeSend: function() {
                    $(".btn-add-output-dest").html("<span class='spinner-border spinner-border-sm me-1'></span>Saving").attr("disabled", true);
                },
                success: function(data) {
                    if (data.success == false) {
                        if(data.isForm == true){
                            msgalert(".output-dest-info-data", data.messages);
                        }else{
                            $(".output-dest-info-data").html(data.messages).show().delay(3000).fadeOut();
                        }
                    } else {
                        $('.output-dest-data').DataTable().ajax.reload();
                        if ($(".output-dest-info-data").hasClass("alert alert-danger")) {
                            $(".output-dest-info-data").removeClass("alert alert-danger");
                        }
                        $(".output-dest-info-data").html(data.messages).show().delay(3000).fadeOut();
                        $('.add-output-dest').modal('hide');
                        form.reset();
                    }
                    $(".btn-add-output-dest").html("<span class='bi bi-save me-1'></span>Save").attr("disabled", false);
                    $('meta[name="csrf-token"').val(data.csrftoken);
                },
                error: function() {
                    $(".btn-add-output-dest").html("<span class='bi bi-save me-1'></span>Save").attr("disabled", false);
                    swal.fire("Add Input Stream Error", "There have problem while adding input stream!", "error");
                }
            });
        });

        $(".output-dest-data").on('click', '.view-output-dest', function(event) {
            var output_dest_data = $(event.currentTarget).attr('data-dest-output-id');
            if (output_dest_data === null) return;
            $.ajax({
                url: "{{ route('outputdest.view') }}",
                type: 'POST',
                data: {id_output_dest: output_dest_data},
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

        $(".output-dest-data").on('click', '.edit-output-dest', function(event) {
            var output_dest_data = $(event.currentTarget).attr('data-dest-output-id');
            if (output_dest_data === null) return;
            $.ajax({
                url: "{{ route('outputdest.edit',['fetch' => 'show']) }}",
                type: 'POST',
                data: {id_output_dest: output_dest_data},
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
                        $('.form-edit-output-dest').submit(function(e) {
                            e.preventDefault();
                            var form = this;
                            var formdata = new FormData(form);
                            formdata.append('id_output_dest', output_dest_data);
                            $.ajax({
                                url: "{{ route('outputdest.edit',['fetch' => 'edit']) }}",
                                type: 'POST',
                                data: formdata,
                                processData: false,
                                contentType: false,
                                async: true,
                                beforeSend: function() {
                                    $(".btn-edit-output-dest").on('.custom-modal-content').html("<span class='spinner-border spinner-border-sm me-1'></span>Saving").attr("disabled", true);
                                },
                                success: function(data) {
                                    $('meta[name="csrf-token"').val(data.csrftoken);
                                    $('input[name=_token]').val(data.csrftoken);
                                    if (data.success == false) {
                                        if(data.isForm == true){
                                            msgalert(".edit-output-dest-info-data", data.messages);
                                        }else{
                                            $(".edit-output-dest-info-data").html(data.messages).show().delay(3000).fadeOut();
                                        }
                                    } else {
                                        $('.output-dest-data').DataTable().ajax.reload();
                                        if ($(".edit-output-dest-info-data").hasClass("alert alert-danger")) {
                                            $(".edit-output-dest-info-data").removeClass("alert alert-danger");
                                        }
                                        $(".edit-output-dest-info-data").html(data.messages).show().delay(3000).fadeOut();
                                        $('.custom-modal-display').modal('hide');
                                        form.reset();
                                    }
                                    $(".btn-edit-output-dest").on('.custom-modal-content').html("<span class='bi bi-save me-1'></span>Save").attr("disabled", false);
                                },
                                error: function() {
                                    $(".btn-edit-output-dest").on('.custom-modal-content').html("<span class='bi bi-save me-1'></span>Save").attr("disabled", false);
                                }
                            });
                        });
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

        $(".output-dest-data").on('click', '.delete-output-dest', function(event) {
            var output_dest_data = $(event.currentTarget).attr('data-dest-output-id');
            if (output_dest_data === null) return;
            Swal.fire({
                title: 'Delete Output Destination',
                text: "This output destination will be erase and you cannot use that again!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('outputdest.delete') }}",
                        type: 'POST',
                        data: {id_output_dest: output_dest_data},
                        async: true,
                        beforeSend: function() {
                            swal.fire({
                                title: "Deleting Output Destination",
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
                            $('.output-dest-data').DataTable().ajax.reload();
                            $('meta[name="csrf-token"').val(data.csrftoken);
                        },
                        error: function(err) {
                            swal.fire("Delete Output Destination Failed", "There have problem while deleting output destination!", "error");
                        }
                    });
                }
            });
            event.preventDefault();
        });
    });
</script>
@endif
<script>
    document.querySelector(".output-dest-data-refresh").addEventListener("click", function(e) {
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
        $('.output-dest-data').DataTable().ajax.reload();
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
@if ($stream_input->is_live != TRUE)
<div class="modal fade add-output-dest" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel"><span class="bi bi-broadcast me-1"></span>Add
                    Output
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-add-output-dest">
                    <div class="form-group mb-2">
                        <label class="form-label">Name Output Destination</label>
                        <input type="text" class="form-control" name="name_output_dest"
                            placeholder="Name Output Destination">
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label">Platform Output</label>
                        <select class="form-select" name="platform_output_dest">
                            <option selected>- Select Platform -</option>
                            <option value="youtube">Youtube</option>
                            <option value="twitch">Twitch</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label">RTMP Output Server</label>
                        <input type="text" class="form-control" name="rtmp_output_server"
                            placeholder="rtmp://xxx.xxx.xxx.xxx/live">
                        <small class="form-text text-muted">*For now we currently not support rtmps:// url
                            server</small>
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label">RTMP Stream Key</label>
                        <div class="input-group" x-data="{ input: 'password' }">
                            <input type="password" class="form-control" name="rtmp_stream_key" x-bind:type="input">
                            <button type="button" class="input-group-text btn-toggle-stream-key"
                                data-bs-toggle="tooltip" data-bs-original-title="Show Stream Key"
                                x-on:click="input = (input === 'password') ? 'text' : 'password'"><span
                                    :class="{'bi bi-eye-slash' : input != 'password','bi bi-eye': input != 'text'}"></span></button>
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label me-1">Status Output</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="status-output-dest-1" value="1"
                                name="status_output_dest">
                            <label class="form-check-label" for="status-output-dest-1">Enable</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="status-output-dest-2" value="0"
                                name="status_output_dest">
                            <label class="form-check-label" for="status-output-dest-2">Disable</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-add-output-dest"><span
                            class="bi bi-save me-1"></span>Save</button>
                </form>
                <div class="my-2 output-dest-info-data"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection