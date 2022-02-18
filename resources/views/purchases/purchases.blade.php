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
                        Sold Products
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
                <table class="table table-striped">
                    <thead>
                        <tr>
                        <th scope="col">#</th>
                        <th scope="col">First</th>
                        <th scope="col">Last</th>
                        <th scope="col">Handle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <th scope="row">1</th>
                        <td>Mark</td>
                        <td>Otto</td>
                        <td>@mdo</td>
                        </tr>
                        <tr>
                        <th scope="row">2</th>
                        <td>Jacob</td>
                        <td>Thornton</td>
                        <td>@fat</td>
                        </tr>
                        <tr>
                        <th scope="row">3</th>
                        <td>Larry</td>
                        <td>the Bird</td>
                        <td>@twitter</td>
                        </tr>
                    </tbody>
                </table>
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
