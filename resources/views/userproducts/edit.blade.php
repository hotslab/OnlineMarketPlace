@extends('layouts.app')

@section('content')<div class="container">
<div class="row justify-content-center">
    <div class="col-md-6">
        @if (\Session::has('failure'))
            <div id="profile" class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>{!! \Session::get('failure') !!}</strong>
            </div>
        @endif
        <div class="text-bold mt-3 md-4">
            <h5>{{ $status == 'edit' ? 'Edit '.$product->name : 'Create Product' }}</h5>
        </div>
        @if($status == 'edit')
            <div class="card my-3 mx-0 text-center" style="width:250px;">
                <img class="card-img-top" src="{{ $product->image }}" alt="Card image cap">
            </div>
        @endif
        <form id="product-create-or-edit" 
            action="{{ $status == 'edit' ? route('userproducts.update', ['id' => $product->id ]) : route('userproducts.store') }} " 
            method="POST"
            files="true"
            enctype="multipart/form-data"
        >
            @csrf
            <div class="form-group">
                <label for="name">Name</label>
                <input name="name" type="text" class="form-control" id="name" placeholder="Name" value="{{ $product->name ?? old('name') }}">
                @foreach ($errors->get('name') as $message)
                    <small class="text-danger">{{ $message }}</small><br/>
                @endforeach
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input name="price" type="number" step="0.01" min="0" class="form-control" id="price" placeholder="Enter price" value="{{ $product->price ?? old('price') }}">
                @foreach ($errors->get('price') as $message)
                    <small class="text-danger">{{ $message }}</small><br/>
                @endforeach
            </div>
            <div class="form-group">
                <label for="image">Image</label>
                <input name="image" type="file" class="form-control" id="password" placeholder="Image" value="{{ old('image') }}">
                @foreach ($errors->get('image') as $message)
                    <small class="text-danger">{{ $message }}</small><br/>
                @endforeach
            </div>
            <div class="form-group mt-3 d-flex justify-content-end">
                <a href="{{ $status == 'edit' ? route('products.show', ['id' => $product->id ]) : route('userproducts.view') }}" class="btn btn-danger">Back</a>
                <button type="submit" class="btn btn-primary" style="margin-left: 10px">Submit</button>
            </div>
        </form>
    </div>
</div>
@endsection