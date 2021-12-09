@extends('layouts.main')

@section('title','Account Settings')

@section('content')
<div class="px-4 py-5 my-5">
    <h1 class="display-5 fw-bold text-center"><span class="bi bi-person-circle me-3"></span>Account Settings</h1>
    <div class="col-xl-5 col-lg-6 mx-auto">
        <div class="card">
            <div class="card-body">
                {!! Session::get('setting_msg') !!}
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-profile-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile"
                            aria-selected="true">Profile</button>
                        <button class="nav-link" id="nav-update-password-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-update-password" type="button" role="tab"
                            aria-controls="nav-update-password" aria-selected="false">Update Password</button>
                        <button class="nav-link" id="nav-session-tab" data-bs-toggle="tab" data-bs-target="#nav-session"
                            type="button" role="tab" aria-controls="nav-session" aria-selected="false">Session</button>
                        <button class="nav-link" id="nav-misc-tab" data-bs-toggle="tab" data-bs-target="#nav-misc"
                            type="button" role="tab" aria-controls="nav-misc" aria-selected="false">Misc</button>
                    </div>
                </nav>
                <div class="tab-content mt-2" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-profile" role="tabpanel"
                        aria-labelledby="nav-profile-tab">
                        <div class="fw-light fs-5">Basic Account</div>
                        <hr>
                        <form enctype="multipart/form-data" action="{{ route('settings.update_profile') }}"
                            method="POST">
                            @csrf
                            <div class="form-group mb-2">
                                <label class="form-label">Fullname</label>
                                <div class="input-group">
                                    <div class="input-group-text"><span class="material-icons">text_format</span></div>
                                    <input type="text" class="form-control @error('fullname') is-invalid @enderror"
                                        name="fullname"
                                        value="{{ (old('fullname')) ? old('fullname') : Auth::user()->name }}">
                                    @error('fullname')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group mb-2">
                                <label class="form-label">Username</label>
                                <div class="input-group">
                                    <div class="input-group-text"><span class="material-icons">account_circle</span>
                                    </div>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                                        name="username"
                                        value="{{ (old('username')) ? old('username') : Auth::user()->username }}">
                                    @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary"><span
                                    class="material-icons me-1">edit</span>Edit</button>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="nav-update-password" role="tabpanel"
                        aria-labelledby="nav-update-password-tab">
                        <div class="fw-light fs-5">Update Password</div>
                        <hr>
                        <form enctype="multipart/form-data" action="{{ route('settings.update_password') }}"
                            method="POST">
                            @csrf
                            <div class="form-group mb-2">
                                <label class="form-label">Old Password</label>
                                <div class="input-group password-toggle">
                                    <input class="form-control @error('old_password') is-invalid @enderror"
                                        name="old_password" id="old-password" type="password">
                                    <button type="button" class="input-group-text btn-toggle-pass"
                                        data-bs-toggle="tooltip" data-bs-original-title="Show Password"><span
                                            class="material-icons">visibility_off</span></button>
                                    @error('old_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row g-3 password-toggle">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">New Password</label>
                                    <input type="password"
                                        class="form-control @error('new_password') is-invalid @enderror"
                                        id="new-password" name="new_password">
                                    @error('new_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password"
                                        class="form-control @error('new_password_confirm') is-invalid @enderror"
                                        id="new-password-confirm" name="new_password_confirm">
                                    @error('new_password_confirm')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary"><span
                                    class="material-icons me-1">key</span>Change
                                Password</button>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="nav-session" role="tabpanel" aria-labelledby="nav-session-tab">
                        <div class="fw-light fs-5">Session</div>
                        <hr>
                        <div class="my-2">
                            @foreach ($session_list as $session)
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="material-icons fs-1">
                                        {{ $session->agent['check_device'] == 'desktop' ? 'monitor' :
                                        ($session->agent['check_device'] == 'phone' ? 'smartphone' :
                                        ($session->agent['check_device'] == 'robot' ? 'smart_toy' : 'devices')) }}
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="fw-light">
                                        {{ $session->agent['platform'].' - '. $session->agent['browser']}}
                                    </div>
                                    <div class="text-muted small">
                                        {{ date('d F Y',$session->last_activity) }}
                                        &bull;
                                        {{ $session->ip_address }}
                                        @if($session->user_agent == Request::header('User-Agent'))
                                        <span class="text-success">This Device</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <button class="btn btn-danger btn-sm" data-bs-toggle="collapse"
                            data-bs-target=".logoutalldevice" aria-expanded="false"
                            aria-controls="logoutalldevice">Logout From All Device</button>
                        <div class="collapse logoutalldevice my-2">
                            <form action="{{ route('settings.logout_all') }}" method="POST">
                                @csrf
                                <div class="form-group mb-2">
                                    <label class="form-label">Password</label>
                                    <div class="input-group">
                                        <div class="input-group-text"><span class="material-icons">lock</span></div>
                                        <input type="password" class="form-control" name="logout_password"
                                            value="{{ old('logout_password') }}">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary"><span
                                        class="material-icons me-1">logout</span>Logout</button>
                            </form>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-misc" role="tabpanel" aria-labelledby="nav-misc-tab">
                        <div class="fw-light fs-5">Miscellaneous</div>
                        <hr>
                        <dl class="misc-section-settings">
                            <dt>Reset To Factory</dt>
                            <dd>
                                <div class="btn btn-danger reset-to-factory">
                                    <span class="material-icons me-1">restart_alt</span>Reset
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
<script>
    document.addEventListener("DOMContentLoaded", function () {
        $(".password-toggle button.btn-toggle-pass").on("click", function (event) {
            event.preventDefault();
            if ($(".password-toggle input").attr("type") == "text") {
                $(".password-toggle input").attr("type", "password");
                $(".password-toggle span").html("visibility_off");
            } else if ($(".password-toggle input").attr("type") == "password") {
                $(".password-toggle input").attr("type", "text");
                $(".password-toggle span").html("visibility");
            }
        });

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
                        url: "{{ route('settings.reset_factory') }}",
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
                            $('input[name="_token"').val(data.csrftoken);
                            $('meta[name="csrf-token"').val(data.csrftoken);
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
    });
</script>
@endsection