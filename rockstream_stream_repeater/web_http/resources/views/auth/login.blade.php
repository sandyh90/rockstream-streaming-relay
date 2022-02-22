@extends('layouts.auth_main')

@section('title','Login')

@section('content')
<form method="POST" action="{{ route('login.process') }}">
    @csrf
    <div class="form-group p-2">
        <label class="form-label">Username</label>
        <div class="input-group">
            <div class="input-group-text"><span class="bi bi-person-circle"></span></div>
            <input id="username" type="text" class="form-control @error('username') is-invalid @enderror"
                name="username" autofocus>
            @error('username')
            <span class="invalid-feedback">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
    <div class="form-group p-2">
        <label class="form-label">Password</label>
        <div class="input-group">
            <div class="input-group-text"><span class="bi bi-lock"></span></div>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                name="password">
            @error('password')
            <span class="invalid-feedback">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
    <div class="d-flex justify-content-between p-2">
        <a class="text-decoration-none" href="{{ route('reset') }}">Reset Password</a>
        <div class="form-check">
            <input type="checkbox" name="rememberme" class="form-check-input" id="remember-me">
            <label class="form-check-label" for="remember-me">Remember Me</label>
        </div>
    </div>
    <div class="form-group p-2">
        <button type="submit" class="btn btn-primary">
            <span class="bi bi-box-arrow-in-right me-1"></span>Login
        </button>
    </div>
</form>
@endsection