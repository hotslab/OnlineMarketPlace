@extends('layouts.app')

@section('content')<div class="container">
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="text-center mt-3 md-4">
            <h3 class="text-primary">Thank you for shopping at the OnlineStore</h3>
            <h5>You just bought :</h5>
            <h4 class="text-primary">{{ $purchase->product->name }} - $ {{ $purchase->product->price }}</h4>
            <p>An email has been sent to <strong>{{ $purchase->email }}</strong> with details about your purchased product.</p>
            <p>Please feel free to continue shopping for anything you like.</p>
            @guest
                <p>Please do not forget that you can sign up and list your own products for sale.</p>
            @endguest
        </div>
    </div>
</div>
@endsection