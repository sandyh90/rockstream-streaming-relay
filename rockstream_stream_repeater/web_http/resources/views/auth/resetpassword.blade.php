@extends('layouts.auth_main')

@section('title','Reset Password')

@section('content')
<div class="small my-2 text-center">For more secure and safe please use strong password and unique character.</div>
<form method="POST" action="{{ route('reset.process') }}">
    @csrf
    <div class="form-group p-2">
        <label class="form-label">Username</label>
        <div class="input-group">
            <div class="input-group-text"><span class="material-icons">account_circle</span></div>
            <input id="username" type="text" class="form-control @error('username') is-invalid @enderror"
                name="username" value="{{ old('username') }}" autofocus>
            @error('username')
            <span class="invalid-feedback">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
    <div class="form-group p-2">
        <div class="d-flex justify-content-between flex-wrap">
            <label class="form-label">Password</label>
            <div data-bs-toggle="collapse" data-bs-target=".info-reset-pass" aria-expanded="false">
                <span class="bi bi-question-circle"></span>
            </div>
        </div>
        <div class=" collapse collapse-horizontal info-reset-pass">
            <div class="small text-muted text-center my-2">You can use reset password from console with using
                artisan command <strong>"php artisan accountmanage:reset"</strong>
            </div>
        </div>
        <div class="input-group">
            <div class="input-group-text"><span class="material-icons">lock</span></div>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                name="password">
            @error('password')
            <span class="invalid-feedback">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
    <div class="form-group p-2">
        <button type="submit" class="btn btn-primary">
            <span class="material-icons me-1">lock_reset</span>Reset Password
        </button>
    </div>
</form>
<div class="text-center small">
    <a class="text-decoration-none" href="{{ route('login') }}">Back To Login</a>
</div>
@endsection