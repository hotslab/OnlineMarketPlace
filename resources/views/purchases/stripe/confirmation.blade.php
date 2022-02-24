@extends('layouts.app')

@section('content')
<div class="container">
    <div id="errorAlert" class="alert alert-danger" style="display: none;" role="alert"></div>
    <div id="successAlert" class="alert alert-success" style="display: none;" role="alert"></div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="text-center mt-3 md-4">
                <h4 class="text-primary">Thank you for shopping at the</h4>
                <h3 class="text-primary">OnlineMarketPlace</h3>
                <h5>You just bought {{ $isDeposit ? 'on deposit' : 'and fully paid'}} :</h5>
                <h4 class="text-primary">{{ $purchase->product->name }} - {{ $purchase->product->currency_symbol }} {{ $paidAmount }}</h4>
                <p>An email has been sent to <strong>{{ $purchase->email }}</strong> with details about your purchased product.</p>
                @if ($isDeposit)
                    <p>
                        <span>As the payment you made is the first deposit of <strong>{{ $purchase->product->currency_symbol }} {{ $paidAmount }}</strong>, </span>
                        <span>the other remaining half which is <strong>{{ $purchase->product->currency_symbol }} {{ $depositLeftAmount }}</strong> will be </span>
                        <span>charged after 5 minutes, and notification will sent to </span>
                        <span>your email address of <strong>{{ $purchase->email }}</strong>.</span> 
                    </p>
                @endif
                <p>Please feel free to continue shopping for anything you like.</p>
                @guest
                    <p>Please do not forget that you can also sign up and list your own products on our site for sale.</p>
                @endguest
            </div>
        </div>
    </div>
</div>
@endsection