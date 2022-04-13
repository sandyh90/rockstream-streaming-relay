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
                                    <div class="input-group-text"><span class="bi bi-type"></span></div>
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
                                    <div class="input-group-text"><span class="bi bi-person-circle"></span>
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
                                    class="bi bi-pencil-square me-1"></span>Edit</button>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="nav-update-password" role="tabpanel"
                        aria-labelledby="nav-update-password-tab">
                        <div class="fw-light fs-5">Update Password</div>
                        <hr>
                        <form enctype="multipart/form-data" action="{{ route('settings.update_password') }}"
                            method="POST">
                            @csrf
                            <div x-data="{ input: 'password' }">
                                <div class="form-group mb-2">
                                    <label class="form-label">Old Password</label>
                                    <div class="input-group password-toggle">
                                        <input class="form-control @error('old_password') is-invalid @enderror"
                                            name="old_password" id="old-password" type="password" x-bind:type="input">
                                        <button type="button" class="input-group-text" data-bs-toggle="tooltip"
                                            data-bs-original-title="Show Password"
                                            x-on:click="input = (input === 'password') ? 'text' : 'password'"><span
                                                :class="{'bi bi-eye-slash' : input != 'password','bi bi-eye': input != 'text'}"></span></button>
                                        @error('old_password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row g-3">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">New Password</label>
                                        <input type="password"
                                            class="form-control @error('new_password') is-invalid @enderror"
                                            id="new-password" name="new_password" x-bind:type="input">
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
                                            id="new-password-confirm" name="new_password_confirm" x-bind:type="input">
                                        @error('new_password_confirm')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary"><span class="bi bi-key me-1"></span>Change
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
                                    <div class="fs-1">
                                        <span
                                            class="bi {{ $session->agent['check_device'] == 'desktop' ? 'bi-pc-display' :
                                            ($session->agent['check_device'] == 'phone' ? 'bi-phone' :
                                            ($session->agent['check_device'] == 'robot' ? 'bi-robot' : 'bi-display')) }}"></span>
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
                                        @if(($session->user_agent == Request::header('User-Agent')) &&
                                        ($session->ip_address == Request::ip()))
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
                                        <div class="input-group-text"><span class="bi bi-lock"></span></div>
                                        <input type="password" class="form-control" name="logout_password"
                                            value="{{ old('logout_password') }}">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary"><span
                                        class="bi bi-box-arrow-right me-1"></span>Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection