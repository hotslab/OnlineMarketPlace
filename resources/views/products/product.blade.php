@extends('layouts.app')

@section('content')
<div class="container">
    <div id="errorAlert" class="alert alert-danger" style="display: none;" role="alert">
    </div>
    <div class="row justify-content-center">
        <div class="card p-3 m-3" style="width: 300px;">
            <div class="online-store-img-container" style="background-image: url({{ asset($product->image) }});"></div>
            <div class="card-body">
                <h4 class="card-title">{{ $product->name }}</h4>
                <p class="card-text">{{ $product->currency_symbol }} {{ $product->price }}</p>
                <div class="mt-3 d-flex justify-content-end">                    
                    @auth
                        @if ($product->userProduct && Auth::user()->id == $product->userProduct->user_id)
                            <a href="{{ route('userproducts.view', ['id' => auth()->user()->id]) }}" class="card-link ml-3 btn btn-danger">Back</a>
                            <a class="card-link ml-3 btn btn-success"
                                onclick="event.preventDefault(); $('#deleteModal').modal('show');"
                            >
                                Delete
                            </a>
                            <a href="{{ route('userproducts.edit', ['status' => 'edit', 'productID' => $product->id ]) }}" 
                                class="card-link ml-3 btn btn-primary"
                            >
                                Edit
                            </a>
                        @else
                            <a href="{{ route('products.view') }}" class="card-link ml-3 btn btn-danger">Back</a>
                            <a href="{{ route('purchases.checkout', ['id' => $product->id ]) }}" class="card-link ml-3 btn btn-success">Buy</a>
                        @endif
                    @else
                        <a href="{{ route('products.view') }}" class="card-link ml-3 btn btn-danger">Back</a>
                        <a href="{{ route('purchases.checkout', ['id' => $product->id ]) }}" class="card-link ml-3 btn btn-success">Buy</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="deleteModal" class="modal bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content text-center p-4">
                <p>Delete <span class="text-primary">{{ $product->name }}</span> ?</p>
                <div class="mt-3 d-flex justify-content-between">
                    <a class="card-link ml-3 btn btn-danger"
                        onclick="event.preventDefault(); $('#deleteModal').modal('hide');"
                    >
                        Cancel
                    </a>
                    <a 
                        class="card-link ml-3 btn btn-success"
                        onclick="deleteProduct( '{{ route('userproduct.destroy', ['id' => $product->id ]) }}' )"
                    >
                        Delete
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function deleteProduct(url) {
        $.ajax({
            method: "DELETE",
            url: url,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: (response) =>{ 
                if (response.status == 'failure') {
                    $('#errorAlert').show()
                    if ($('#errorAlert')) $('#errorAlert')[0].innerHTML = response.message
                    $('#deleteModal').modal('hide');
                } else window.location.replace(response.url)
            },
            error: (XMLHttpRequest, textStatus, errorThrown) => { 
                $('#errorAlert').show()
                if ($('#errorAlert')) $('#errorAlert')[0].innerHTML = errorThrown
                $('#deleteModal').modal('hide');
            }       
        })
    }
</script>
@endsection
