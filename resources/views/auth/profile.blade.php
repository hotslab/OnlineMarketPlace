@extends('layouts.app')

@section('content')<div class="container">
<div class="row justify-content-center">
    <div class="col-md-6">
        @if (\Session::has('success'))
            <div id="profile" class="alert alert-success alert-dismissible fade show" role="alert">
                {!! \Session::get('success') !!}
            </div>
        @endif
        <div class="text-bold mt-3 md-4">
            <h5>Update Profile</h5>
        </div>
        <form id="update-user-form" class="mt-4" method="POST" action="{{ route('profile.update') }}">
            @csrf
            <div class="form-group">
                <label for="name">Name</label>
                <input name="name" type="text" class="form-control" id="name" placeholder="Name" value="{{ old('name') ?? $user->name }}">
                @foreach ($errors->get('name') as $message)
                    <small class="text-danger">{{ $message }}</small><br/>
                @endforeach
            </div>
            <div class="form-group">
                <label for="surname">Surname</label>
                <input name="surname" type="text" class="form-control" id="surname" placeholder="Surname" value="{{ old('surname') ?? $user->surname }}">
                @foreach ($errors->get('surname') as $message)
                    <small class="text-danger">{{ $message }}</small><br/>
                @endforeach
            </div>
            <div class="form-group">
                <label for="email">Email address</label>
                <input name="email" type="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="Enter email" value="{{ old('email') ?? $user->email }}">
                @foreach ($errors->get('email') as $message)
                    <small class="text-danger">{{ $message }}</small><br/>
                @endforeach
            </div>
            <div class="form-group mt-3 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
        <div class="text-bold mt-3 md-4">
            <h5>Change Password</h5>
        </div>
        <form id="update-user-form" class="mt-4" method="POST" action="{{ route('profile.passwordreset') }}">
            @csrf
            <div class="form-group">
                <label for="password">Password</label>
                <input name="password" type="password" class="form-control" id="password" placeholder="Password" value="{{ old('password') }}">
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
            <div class="form-group mt-3 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>
@endsection