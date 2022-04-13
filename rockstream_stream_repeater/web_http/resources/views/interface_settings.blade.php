@extends('layouts.main')

@section('title','Interface Settings')

@section('head-content')
<!-- Seperate Addons CSS -->
<link rel="stylesheet" href="{{ asset('assets/vendor/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/select2/css/select2-bootstrap4.min.css') }}">
@endsection

@section('content')
<div class="px-4 py-5 my-5">
    <h1 class="display-5 fw-bold text-center"><span class="bi bi-tools me-3"></span>Interface Settings
    </h1>
    <div class="col-xl-5 col-lg-6 mx-auto">
        <div class="card">
            <div class="card-body">
                {!! Session::get('itf_setting_msg') !!}
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-misc-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-misc" type="button" role="tab" aria-controls="nav-misc"
                            aria-selected="true">Misc</button>
                    </div>
                </nav>
                <div class="tab-content mt-2" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-misc" role="tabpanel" aria-labelledby="nav-misc-tab">
                        <div class="fw-light fs-5">Miscellaneous</div>
                        <hr>
                        <dl class="misc-section-settings">
                            <dt>Launch Test Stream</dt>
                            <dd>
                                <div class="btn-group test-streaming-btn-group">
                                    <div class="btn btn-primary launch-test-stream">
                                        <span class="bi bi-film me-1"></span>Test Stream
                                    </div>
                                    <div class="btn btn-info launch-daemon-test-stream">
                                        <span class="bi bi-cpu me-1"></span>Launch Daemon
                                    </div>
                                </div>
                                <div class="small text-muted">*Daemon will automatically stop if there no queue test
                                    stream.</div>
                            </dd>
                            <dt>Reset To Factory</dt>
                            <dd>
                                <div class="btn btn-danger reset-to-factory">
                                    <span class="bi bi-arrow-counterclockwise me-1"></span>Reset
                                </div>
                            </dd>
                        </dl>
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
    document.addEventListener("DOMContentLoaded", function () {
        $(".misc-section-settings").on('click', '.reset-to-factory', function(event) {
            Swal.fire({
                title: 'Reset To Factory',
                text: "Are you sure you want to reset to factory?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Reset'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('interfaces.reset_factory') }}",
                        type: 'POST',
                        async: true,
                        beforeSend: function() {
                            swal.fire({
                                title: "Resetting...",
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
                            $('meta[name="csrf-token"]').val(data.csrftoken);
                            location.reload();
                        },
                        error: function(err) {
                            swal.fire("Reset To Factory Failed", "There have problem while reset to factory!", "error");
                        }
                    });
                }
            });
            event.preventDefault();
        });

        $(".misc-section-settings").on('click', '.launch-test-stream', function(event) {
            $.ajax({
                url: "{{ route('interfaces.start_teststream',['fetch' => 'show']) }}",
                type: 'POST',
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
                        $('.form-start-test-streaming').submit(function(e) {
                            e.preventDefault();
                            var form = this;
                            var formdata = new FormData(form);
                            $.ajax({
                                url: "{{ route('interfaces.start_teststream',['fetch' => 'start']) }}",
                                type: 'POST',
                                data: formdata,
                                processData: false,
                                contentType: false,
                                async: true,
                                beforeSend: function() {
                                    $(".btn-start-test-streaming").on('.custom-modal-content').html("<span class='spinner-border spinner-border-sm me-1'></span>Starting Test Stream").attr("disabled", true);
                                },
                                success: function(data) {
                                    $('meta[name="csrf-token"]').val(data.csrftoken);
                                    $('input[name=_token]').val(data.csrftoken);
                                    if (data.success == false) {
                                        if(data.isForm == true){
                                            msgalert(".start-test-streaming-info-data", data.messages);
                                        }else{
                                            $(".start-test-streaming-info-data").html(data.messages).show().delay(3000).fadeOut();
                                        }
                                    } else {
                                        $('.premiere-video-data').DataTable().ajax.reload();
                                        if ($(".start-test-streaming-info-data").hasClass("alert alert-danger")) {
                                            $(".start-test-streaming-info-data").removeClass("alert alert-danger");
                                        }
                                        $(".start-test-streaming-info-data").html(data.messages).show().delay(3000).fadeOut();
                                        $('.custom-modal-display').modal('hide');
                                        form.reset();
                                    }
                                    $(".btn-start-test-streaming").on('.custom-modal-content').html("<span class='bi bi-broadcast me-1'></span>Start Test").attr("disabled", false);
                                },
                                error: function() {
                                    $(".btn-start-test-streaming").on('.custom-modal-content').html("<span class='bi bi-broadcast me-1'></span>Start Test").attr("disabled", false);
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

        $(".misc-section-settings").on('click', '.launch-daemon-test-stream', function(event) {
            $.ajax({
                url: "{{ route('interfaces.launch_daemon_teststream') }}",
                type: 'OPTIONS',
                async: true,
                beforeSend: function() {
                    $(".launch-daemon-test-stream").on('.test-streaming-btn-group').html("<span class='spinner-border spinner-border-sm me-1'></span>Launching Daemon").attr("disabled", true);
                   swal.fire({
                        title: "Launching Daemon For Test Streaming",
                        text: "Please wait",
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });
                    Swal.showLoading();
                },
                success: function(data) {
                    $(".launch-daemon-test-stream").on('.test-streaming-btn-group').html("<span class='bi bi-cpu me-1'></span>Launch Daemon").attr("disabled", false);
                    swal.fire({
                        icon: data.alert.icon,
                        title: data.alert.title,
                        text: data.alert.text,
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    });
                    $('meta[name="csrf-token"]').val(data.csrftoken);
                    $('input[name=_token]').val(data.csrftoken);
                },
                error: function(err) {
                    $(".launch-daemon-test-stream").on('.test-streaming-btn-group').html("<span class='bi bi-cpu me-1'></span>Launch Daemon").attr("disabled", false);
                    swal.fire("Launching Daemon Test Streaming Failed", "There have problem while launching daemon test streaming!", "error");
                }
            });
            event.preventDefault();
        });
    });
</script>
<script>
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