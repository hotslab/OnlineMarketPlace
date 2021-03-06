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
use App\Jobs\ProcessStripePaymentIntent;
use Stripe;

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
            'p.image',
            'p.name as product_name',
            'p.price',
            'p.currency',
            'p.currency_symbol',
            DB::raw('u.*'), 
            'pr.id as purchase_id', 
            'pr.email as purchaser_email',
            'pr.paid_amount',
            'pr.is_deposit',
            'pr.created_at as bougnt_date',
            'up.id as user_product_id'
        )->orderBy("bougnt_date", "DESC")->paginate(15);
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
            'ignoreSavedDetails' => ['required']
        ]);
        if ($validator->fails()) { 
            $errorMessage = "";
            foreach ($validator->errors()->get('email') as $key => $message) { $errorMessage .= ($key + 1).". ".$message." "; }
            foreach ($validator->errors()->get('ignoreSavedDetails') as $key => $message) { $errorMessage .= ($key + 1).". ".$message." "; }
            return response()->json([
                'status' => 'failure',
                'code' => 500,
                'message' => $errorMessage
            ], 500);
        } 
        try {
            $product = Product::find($request->input('productID'));
            if ($product->userProduct->user->email == $request->input('email')) {
                return response()->json([
                    'status' => 'failure',
                    'code' => 500,
                    'message' => "You cannot purchase this product as your are its seller based on your email address."
                ], 500);
            }
            $stripe = new Stripe\StripeClient(env('STRIPE_SECRET'));
            $customer = null;
            $customers = $stripe->customers->all(['email' => $request->input('email')]);
            $customer = collect($customers->data)->first();
            if (!$customer) {
                $customer = $stripe->customers->create([
                    'email' => $request->input('email')
                ]);
            }
            $setupIntent = null;
            if ($request->input('ignoreSavedDetails') == 'true') {
                $response = $this->createNewStripeIntent($request, $customer, $stripe);
                return $response['code'] == 200 ? response()->json($response, 200) : response()->json($response, 500);
            } else {
                $setupIntents = $stripe->setupIntents->all(['customer' => $customer->id ]);
                $setupIntent = collect($setupIntents->data)->filter(function($intent) { return $intent->status == 'succeeded'; })->sortBy('created')->values()->first();
                if (!$setupIntent) {
                    $response = $this->createNewStripeIntent($request, $customer, $stripe);
                    return $response['code'] == 200 ? response()->json($response, 200) : response()->json($response, 500);
                } else {
                    return response()->json([
                        'hasSavedDetails' => true,
                        'clientSecret' => $setupIntent->client_secret,
                        'customer' => $customer,
                        'setupIntent' => $setupIntent,
                        'code' => 200,
                        'status' => 'success'
                    ], 200);
                }
            }
        } catch (Error $e) {
            return response()->json([
                "code" => 500,
                'message' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    protected function createNewStripeIntent(Request $request, $customer, $stripe)
    {
        $setupIntent = $stripe->setupIntents->create([
            'customer' => $customer->id, 
            'payment_method_types' => ['card'],
            'description' => 'Card payment details for '.$request->input('email'),
            'metadata'=> ['customer_email' => $request->input('email')],
            'usage' => 'off_session'
        ]);
        if ($setupIntent) {
            return [
                'hasSavedDetails' => false,
                'clientSecret' => $setupIntent->client_secret,
                'customer' => $customer,
                'setupIntent' => $setupIntent,
                'code' => 200,
                'status' => 'success'
            ];
        } else {
            return [
                "code" => 500,
                'message' => 'Payment setup could not be completed due to an unknow error. Please try again or contact support.',
                'status' => 'error'
            ];
        }
    }

    public function purchase(Request $request) 
    {
        $product = Product::find($request->input("productID"));
        $purchase = Purchase::create([
            "product_id" =>  $request->input("productID"),
            "email" => $request->input("email"),
            "paid_amount" => $request->input("paidAmount"),
            "is_deposit" => $product->price > $request->input("paidAmount")
        ]);
        if ($purchase) {
            return response()->json([
                'redirectURL' => route('purchases.confirmation', ['id' => $purchase->id]),
                'deleteURL' => route('purchases.delete', ['id' => $purchase->id]), 
                'code' => 200,
                'status' => 'success'
            ], 200);
        }
    }

    public function confirmation($id) 
    {
        $purchase = Purchase::find($id);
        $isDeposit = ($purchase->product->price * 100) > ($purchase->paid_amount * 100) ? true : false;
        $depositLeftAmount = null;
        ProcessStripePaymentIntent::dispatchAfterResponse($purchase->id, $purchase->paid_amount, $isDeposit, false);
        if ($isDeposit) {
            $depositLeftAmount = $purchase->product->price - $purchase->paid_amount;
            ProcessStripePaymentIntent::dispatch($purchase->id, $depositLeftAmount, $isDeposit, true)
            ->delay(now()->addMinutes(5));
        }
        return View::make('purchases.stripe.confirmation', [
            'purchase' => $purchase, 'isDeposit' => $isDeposit, 'paidAmount' => $purchase->paid_amount, 'depositLeftAmount' => $depositLeftAmount
        ]);
    }

    public function destroyPurchase($id) 
    {
        Purchase::destroy($id);
    }
}
