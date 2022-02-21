@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center text-bold m-0 mt-3 md-4">
        <h5>Manage Your Products</h5>
        <a href="{{ route('userproducts.edit', ['status' => 'create', 'productID' => null ]) }}" class="btn btn-primary">Add New Product</a>
    </div>
    <div class="row justify-content-center">
        @foreach ($userProducts as $userProduct)
            <div class="card m-3 p-2" style="width:250px;">
                <img class="card-img-top" src="{{ asset($userProduct->product->image) }}" alt="Card image cap">
                <div class="card-body">
                    <h6 class="card-title">{{ $userProduct->product->name }}</h6>
                    <p class="card-text">{{ $userProduct->product->currency_symbol }} {{ $userProduct->product->price }}</p>
                    <div class="mt-3 d-flex justify-content-end">
                        <a href="{{ route('products.show', ['id' => $userProduct->product->id ]) }}" class="btn btn-primary">View</a>
                    </div>
                </div>
            </div>
        @endforeach
        @if(count($userProducts) <= 0)
            <div class="card mt-3 mb-3 p-5 w-100" style="width:250px;">
                <div class="card-body">
                    <h5 class="card-title text-center">No records found</h5>
                </div>
            </div>
        @endif
    </div>
    <div class="d-flex mt-3 justify-content-center">
        {{ $userProducts->links('vendor.pagination.bootstrap-4') }}
    </div>
</div>
@endsection
