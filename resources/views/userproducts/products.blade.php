@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center text-bold m-0 mt-3 md-4">
        <h5>Manage Your Products</h5>
        <a href="{{ route('userproducts.edit', ['status' => 'create', 'productID' => null ]) }}" class="btn btn-primary">Add New Product</a>
    </div>
    <div class="row justify-content-center">
        @if(count($userProducts) > 0)
            <div class="card my-3 mx-0 p-0 w-100">
                <div class="card-body m-0 p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Image</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">View</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userProducts as $userProduct)    
                                    <tr>
                                        <td class="onlinestore-table-img" style="background-image: url({{ asset($userProduct->product->image) }});"></td>
                                        <td>{{ $userProduct->product->name }}</td>
                                        <td>{{ $userProduct->product->currency_symbol }} {{ $userProduct->product->price }}</td>
                                        <td>{{ date_format(date_create($userProduct->created_at), "Y-m-d") }}</td>
                                        <td>
                                            <a href="{{ route('products.show', ['id' => $userProduct->product->id ]) }}" class="btn btn-primary">Edit</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="card mt-0 mb-3 p-5 w-100" style="width:250px;">
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
