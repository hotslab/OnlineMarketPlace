@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="card p-3 m-3" style="width: 18rem;">
            <img class="card-img-top" src="{{ $product->image }}" alt="Card image cap">
            <div class="card-body">
                <h4 class="card-title">{{ $product->name }}</h4>
                <span class="card-text">$ {{ $product->price }}</span><br/>
                <div class="mt-3 d-flex justify-content-end">
                    <a href="{{ route('products.view') }}" class="card-link ml-3 btn btn-danger">Back</a>
                    <a href="#" class="card-link ml-3 btn btn-success">Edit</a>
                    <a href="#" class="card-link ml-3 btn btn-primary">Buy</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
