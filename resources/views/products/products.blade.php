@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        @foreach ($products as $product)
            <div class="card m-3 p-2" style="width:250px;">
                <img class="card-img-top" src="{{ $product->image }}" alt="Card image cap">
                <div class="card-body">
                    <h6 class="card-title">{{ $product->name }}</h6>
                    <span class="card-text">$ {{ $product->price }}</span><br/>
                    <a href="{{ route('products.show', ['id' => $product->id ]) }}" class="btn btn-primary">View</a>
                </div>
            </div>
        @endforeach
    </div>
    <div class="d-flex mt-3 justify-content-center">
        {{ $products->links('vendor.pagination.bootstrap-4') }}
        <!-- <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                <li class="page-item"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">Next</a></li>
            </ul>
        </nav> -->
    </div>
</div>
@endsection
