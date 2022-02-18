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
                <h5>Forgot Password</h5>
            </div>
            <form id="forgot-password-form" action="{{ route('password.reset') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input name="email" type="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="Enter email" value="{{ old('email') }}">
                    @foreach ($errors->get('email') as $message)
                        <small class="text-danger">{{ $message }}</small><br/>
                    @endforeach
                </div>
                <div class="form-group mt-3 d-flex justify-content-between align-items-center">
                    <a href="{{ route('login.view') }}" class="card-link btn btn-danger ml-3">Cancel</a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection