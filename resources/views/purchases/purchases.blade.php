@extends('layouts.app')

@section('content')
<div class="container">
    <div class="text-bold mt-3 md-4">
        <h5>Purchases</h5>
    </div>
    <div class="row justify-content-center">
        <div class="col-12">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="{{ $type == 'customer' ? 'nav-link active' : 'nav-link'}}" 
                        href="{{ route('purchases.view', ['id' => auth()->user()->id, 'type' => 'customer']) }}"
                    >
                        Sales
                    </a>
                </li>
                <li class="nav-item">
                    <a class="{{ $type == 'user' ? 'nav-link active' : 'nav-link'}}" 
                        href="{{ route('purchases.view', ['id' => auth()->user()->id, 'type' => 'user']) }}"
                    >
                        Bought Products
                    </a>
                </li>
            </ul>
            @if(count($purchases) > 0)
            <div class="card m-0 p-0 w-100">
                <div class="card-body m-0 p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Image</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Amount Paid</th>
                                    <th scope="col">Purchaser Email</th>
                                    <th scope="col">Is Deposit ?</th>
                                    <th scope="col">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchases as $key => $purchase)    
                                    <tr>
                                        <td>
                                            <img src="{{ asset($purchase->image) }}" alt="" style="width: 50px; height: 50px">
                                        </td>
                                        <td>{{ $purchase->product_name }}</td>
                                        <td>{{ $purchase->currency_symbol }} {{ $purchase->price }}</td>
                                        <td>{{ $purchase->currency_symbol }} {{ $purchase->paid_amount }}</td>
                                        <td>{{ $purchase->purchaser_email }}</td>
                                        <td>{{ $purchase->is_deposit ? 'true' : 'false' }}</td>
                                        <td>{{ date_format(date_create($purchase->bougnt_date), "Y-m-d") }}</td>
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
    </div>
    <div class="d-flex mt-3 justify-content-center">
        {{ $purchases->links('vendor.pagination.bootstrap-4') }}
    </div>
</div>
@endsection
