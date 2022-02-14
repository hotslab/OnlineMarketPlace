<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\Product;
use App\Models\UserProduct;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::orderBy("created_at", "DESC")->paginate(15);
        return View::make('products.products', ['products' => $products ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric' ],
            'image' => ['required', 'file', 'image']
        ]);
        if ($validator->fails()) { return back()->withErrors($validator)->withInput(); } 
        $path = $request->file('image')->store('products');
        if (!$path) {
            return back()->with('failure', 'Image could not be saved due to unknown error. Please try again');
        }
        $product = Product::create([
            "image" => $path,
            "name" => $request->input("name"),
            "price" => $request->input("price"),
        ]);
        if ($product) {
            $userProduct = UserProduct::create([
                "product_id" => $product->id,
                "user_id" => Auth::user()->id
            ]);
            if ($userProduct) {
                return redirect()->route('userproducts.view');
            } else {
                $product->delete();
                return back()->with('failure', 'New product could not be linked to user. Please try again.');
            }
        } else {
            return back()->with('failure', 'New product could not be saved. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);
        return View::make('products.product', ['product' => $product, "images" => Storage::url($product->image) ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric' ],
            'image' => ['required', 'file', 'image']
        ]);
        if ($validator->fails()) { return back()->withErrors($validator)->withInput(); } 
        $path = $request->file('image')->store('products');
        if (!$path) {
            return back()->with('failure', 'New image could not be saved due to unknown error. Please try again');
        }
        $product = Product::where("id", $id)->update([
            "image" => $path,
            "name" => $request->input("name"),
            "price" => $request->input("price"),
        ]);
        if ($product) {
            return redirect()->route('userproducts.view');
        } else {
            return back()->with('failure', 'New product could not be updated. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function userProducts(Request $request)
    {
        $userProducts = UserProduct::orderBy("created_at", "DESC")->paginate(15);
        return View::make('userproducts.products', ['userProducts' => $userProducts ]);
    }

    public function userProductEdit(Request $request)
    {
        return View::make('userproducts.edit', [
            'status' => $request->input('status'),
            'product' => $request->has('productID') ? Product::find($request->input('productID')) : null
        ]);
    }
}
