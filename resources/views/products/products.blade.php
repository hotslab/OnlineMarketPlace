@extends('layouts.app')

@section('content')
<div class="container">
    <div class="text-bold mt-3 md-4">
        <h5>Products for Sale</h5>
    </div>
    <div class="row justify-content-center">
        @foreach ($products as $product)
            <div class="card m-3 p-2" style="width:250px;">
                <img class="card-img-top" src="{{ $product->image }}" alt="Card image cap">
                <div class="card-body">
                    <h6 class="card-title">{{ $product->name }}</h6>
                    <p class="card-text">$ {{ $product->price }}</p>
                    <div class="mt-3 d-flex justify-content-end">
                        <a href="{{ route('products.show', ['id' => $product->id, 'origin' => 'customer' ]) }}" class="btn btn-primary">View</a>
                    </div>
                </div>
            </div>
        @endforeach
        @if(count($products) <= 0)
            <div class="card mt-3 mb-3 p-5 w-100" style="width:250px;">
                <div class="card-body">
                    <h5 class="card-title text-center">No records found</h5>
                </div>
            </div>
        @endif
    </div>
    <div class="d-flex mt-3 justify-content-center">
        {{ $products->links('vendor.pagination.bootstrap-4') }}
    </div>
</div>
@endsection
