@extends('layouts.app')

@section('content')<div class="container">
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="text-bold mt-3 md-4">
            <h5>Login</h5>
        </div>
        <form id="login-form" action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Email address</label>
                <input name="email" type="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="Enter email" value="{{ old('email') }}">
                @foreach ($errors->get('email') as $message)
                    <small class="text-danger">{{ $message }}</small><br/>
                @endforeach
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input name="password" type="password" class="form-control" id="password" placeholder="Password" value="{{ old('password') }}">
                @foreach ($errors->get('password') as $message)
                    <small class="text-danger">{{ $message }}</small><br/>
                @endforeach
            </div>
            <div class="form-group mt-3">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>
@endsection