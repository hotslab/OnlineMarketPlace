@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="card p-3 m-3" style="width: 250px;">
            <img class="card-img-top" src="{{ asset($product->image) }}" alt="Card image cap">
            <div class="card-body">
                <h4 class="card-title">{{ $product->name }}</h4>
                <p class="card-text">$ {{ $product->price }}</p>
                <div class="mt-3 d-flex justify-content-end">
                    <a href="{{ route('products.view') }}" class="card-link ml-3 btn btn-danger">Back</a>
                    @auth
                        @if ($product->userProduct && Auth::user()->id == $product->userProduct->user_id)
                            <a href="{{ route('userproducts.edit', ['status' => 'edit', 'productID' => $product->id ]) }}" 
                                class="card-link ml-3 btn btn-success"
                            >
                                Edit
                            </a>
                        @else
                            <a href="#" class="card-link ml-3 btn btn-primary">Buy</a>
                        @endif
                    @else
                        <a href="#" class="card-link ml-3 btn btn-primary">Buy</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
