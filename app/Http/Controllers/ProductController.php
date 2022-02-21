<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\UserProduct;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;
USE Stripe;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::from('products as p')
        ->leftJoin('user_products as up', 'up.product_id', '=', 'p.id')
        ->leftJoin('users as u', 'u.id', '=', 'up.user_id')
        ->where(function ($query) {
            if (Auth::user()) { $query->where('u.id', '<>', Auth::user()->id); }
        })->select( DB::raw('p.*') )->orderBy("created_at", "DESC")->paginate(15);
        return View::make('products.products', ['products' => $products ]);
    }

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
            "name" => $request->input("name"),
            "price" => $request->input("price"),
            "image" => $path
        ]);
        if ($product) {
            $userProduct = UserProduct::create([
                "product_id" => $product->id,
                "user_id" => Auth::user()->id
            ]);
            if ($userProduct) {
                return redirect()->route('userproducts.view', ['id' => Auth::user()->id]);
            } else {
                Storage::delete($product->image);
                $product->delete();
                return back()->with('failure', 'New product could not be linked to user. Please try again.');
            }
        } else {
            return back()->with('failure', 'New product could not be saved. Please try again.');
        }
    }

    public function show(Request $request, $id)
    {
        $product = Product::find($id);
        return View::make('products.product', ['product' => $product ]);
    }

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
            "name" => $request->input("name"),
            "price" => $request->input("price"),
            "image" => $path
        ]);
        if ($product) {
            return redirect()->route('userproducts.view', ['id' => Auth::user()->id]);
        } else {
            return back()->with('failure', 'New product could not be updated. Please try again.');
        }
    }

    public function destroy($id)
    {
        $deletedUserProduct = UserProduct::where('product_id', $id)->where('user_id', Auth::user()->id)->delete();
        if ($deletedUserProduct) {
            Product::find($id)->delete();
            return response()->json([
                'status' => 'success',
                'url' => route('userproducts.view', ['id' => Auth::user()->id]),
                'message' => null
            ]);
        } else {
            return response()->json([
                'status' => 'failure',
                'url' => route('products.show', ['id' =>$id]),
                'message' => 'Product could not be deleted due to uknown error. Please try again.'
            ]);
        }
    }

    public function userProducts($id)
    {
        $userProducts = UserProduct::where("user_id", $id)->orderBy("created_at", "DESC")->paginate(15);
        return View::make('userproducts.products', ['userProducts' => $userProducts ]);
    }

    public function userProductEdit(Request $request)
    {
        return View::make('userproducts.edit', [
            'status' => $request->input('status'),
            'product' => $request->has('productID') ? Product::find($request->input('productID')) : null
        ]);
    }

    public function userPurchases(Request $request, $id)
    {
        $purchases = Purchase::from('purchases as pr')
        ->leftJoin('products as p', 'p.id', '=', 'pr.product_id')
        ->leftJoin('user_products as up', 'up.product_id', '=', 'p.id')
        ->leftJoin('users as u', 'u.id', '=', 'up.user_id')
        ->where(function ($query) use ($id, $request) {
            if ($request->input('type') == 'customer') {
                $query->where('u.id', $id)->where('pr.email', '<>', Auth::user()->email);
            } else {
                $query->where('u.id', '<>', $id)->where('pr.email', Auth::user()->email);
            }
        })->select( 
            DB::raw('p.*'), 
            DB::raw('u.*'), 
            DB::raw('pr.id as purchase_id'), 
            DB::raw('pr.email as purchaser_email'),
            DB::raw('up.id as user_product_id')
        )->paginate(15);
        return View::make('purchases.purchases', ['type' => $request->input("type"), 'purchases' => $purchases ]);
    }


    public function checkout($id) 
    {
        return View::make('purchases.stripe.checkout', ['product' => Product::find($id)]);
    }


    public function clientToken(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);
        if ($validator->fails()) { 
            $errorMessage = "";
            foreach ($validator->errors()->get('email') as $message) { $errorMessage .= $message."\n\n"; }
            return response()->json([
                'status' => 'failure',
                'code' => 500,
                'message' => $errorMessage
            ], 500);
        } 
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Create a PaymentIntent with amount and currency
            $paymentIntent = Stripe\PaymentIntent::create([
                'amount' => $request->input("price") * 100,
                'currency' => $request->input("currency"),
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);
            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'code' => 200,
                'status' => 'success'
            ], 200);
        } catch (Error $e) {
            return response()->json([
                "code" => 500,
                'message' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    public function purchase(Request $request) 
    {
        
    }

    public function confirmation($id) 
    {
        return View::make('purchases.stripe.confirmation', ['purchase' => Purchase::find($id)]);
    }

    protected function sendPurchaseEmail(Purchase $purchase) {
        $response = Http::get('https://api.elasticemail.com/v2/email/send', [
            'apikey' => env('ELASTIC_EMAIL_API'),
            'subject' => 'New product purchased - '.$purchase->product->name,
            'from' => env('ELASTIC_EMAIL_SENDER_ADDRESS'),
            'to' => $purchase->email,
            'bodyHtml' => 
                '<div style="text-align:center; padding: 30px;">'.
                    '<h2 style="color:#1E90FF;">Thank you for shopping at the OnlineStore</h2>'.
                    '<p>You have successfully purchased the following product at our online store:</p>'.
                    '<table style="font-family: Arial, Helvetica, sans-serif;border-collapse: collapse;width: 100%; margin-top: 20px;">'.
                        '<thead style="padding-top: 12px;padding-bottom: 12px;text-align: left;background-color: #1E90FF;color: white;">'.
                            '<tr>'.
                                '<th style="border:1px solid #ddd;padding: 8px;">Name</th>'.
                                '<th style="border:1px solid #ddd;padding: 8px;">Price</th>'.
                                '<th style="border:1px solid #ddd;padding: 8px;">Quantity (Units)</th>'.
                            '</tr>'.
                        '</thead>'.
                        '<tbody style="text-align: left;">'.
                            '<tr>'.
                                '<td style="border:1px solid #ddd;padding: 8px;">'.$purchase->product->name.'</td>'.
                                '<td style="border:1px solid #ddd;padding: 8px;">'.$purchase->product->currency_symbol.' '.$purchase->product->price.'</td>'.
                                '<td style="border:1px solid #ddd;padding: 8px;">1</td>'.
                            '</tr>'.
                        '</tbody>'.
                    '</table>'.
                    '<p style="margin-top: 30px">&copy; OnlineStore 2022</p>'.
                '</div>'
        ]);
    }
}
