@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            @if (\Session::has('failure'))
                <div id="profile" class="alert alert-danger alert-dismissible fade show" role="alert">
                    {!! \Session::get('failure') !!}
                </div>
            @endif
            <div class="text-bold mt-3 md-4">
                <h5>Reset Password</h5>
                <p>An email was sent to <strong>{{ $user->email }}</strong> with your OTP to reset your password. Please check in all your folders.</p>
            </div>
            <form id="forgot-password-form" action="{{ route('password.confirm') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="password">OTP Token</label>
                    <input name="otp" type="name" class="form-control" id="otp" placeholder="Enter otp token" value="{{ old('otp') }}">
                    @foreach ($errors->get('otp') as $message)
                        <small class="text-danger">{{ $message }}</small><br/>
                    @endforeach
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input name="password" type="password" class="form-control" id="password" placeholder="Enter password" value="{{ old('password') }}">
                    @foreach ($errors->get('password') as $message)
                        <small class="text-danger">{{ $message }}</small><br/>
                    @endforeach
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input name="password_confirmation" type="password" class="form-control" id="password_confirmation" placeholder="Confirm Password" value="{{ old('password_confirmation') }}">
                    @foreach ($errors->get('password_confirmation') as $message)
                        <small class="text-danger">{{ $message }}</small><br/>
                    @endforeach
                </div>
                <div class="form-group d-none">
                    <input name="user_id" type="number" class="form-control" id="user_id" value="{{ $user->id }}">
                </div>
                <div class="form-group mt-3 d-flex justify-content-between align-items-center">
                    <a href="{{ route('password.forgot') }}" class="btn btn-danger">Restart</a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection