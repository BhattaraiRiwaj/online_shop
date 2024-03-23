<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Country;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Models\CustomerAddress;
use App\Models\Shipping;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{

    public function addToCart(Request $request)
    {
        $products = Product::with('product_images')->find($request->id);

        if ($products == null) {
            return response()->json([
                'status' => false,
                'message' => "Product Not Found"
            ]);
        }
        if (Cart::count() > 0) {
            //products Found in cart
            //check if this product already exist
            //Return message that product already exist in cart
            //if product not found in the cart,then add product in cart.
            $cartContent = Cart::content();
            $productAlreadyExist = false;
            foreach ($cartContent as $content) {
                if ($content->id == $products->id) {
                    $productAlreadyExist = true;
                }
            }
            if ($productAlreadyExist == false) {
                Cart::add(
                    $products->id,
                    $products->title,
                    1,
                    $products->price,
                    ['productImage' => (!empty($products->product_images)) ? $products->product_images->first() : '']
                );

                $status = true;
                $message = '<strong>' . $products->title . '</strong> Added In Your Cart Successfully';
                $request->session()->flash('success', $message);
            } else {
                $status = false;
                $message = $products->title . " Is Already Added In cart";
            }
        } else {
            // cart is empty
            Cart::add(
                $products->id,
                $products->title,
                1,
                $products->price,
                ['productImage' => (!empty($products->product_images)) ?
                    $products->product_images->first() : '']
            );

            $status = true;
            $message = '<strong>' . $products->title . '</strong> Added In Your Cart Successfully';
            $request->session()->flash('success', $message);
        }
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }


    public function cart()
    {
        $carts = Cart::content();
        // dd($carts);
        return view('front.cart', compact('carts'));
    }


    public function updateCart(Request $request)
    {

        $rowId = $request->rowId;
        $qty = $request->qty;

        $itemInfo = Cart::get($rowId);

        $product = Product::find($itemInfo->id);
        // dd($product);
        //check qty available in stock
        if ($product->track_qty == "Yes") {
            if ($qty <= $product->qty) {
                Cart::update($rowId, $qty);
                $status = true;
                $message = "cart updated successfully";
                $request->session()->flash('success', $message);
            } else {
                $status = false;
                $message = "Requested qty('$qty') is not available in stock.";
                $request->session()->flash('error', $message);
            }
        } else {
            Cart::update($rowId, $qty);
            $status = true;
            $message = "cart updated successfully";
            $request->session()->flash('success', $message);
        }


        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function deleteItem(Request $request)
    {

        $itemInfo = Cart::get($request->rowId);
        if ($itemInfo == null) {
            $errorMessage = 'Item Not found in Cart';
            $request->session()->flash('error', $errorMessage);
            return response()->json([
                'status' => false,
                'message' => $errorMessage
            ]);
        }

        Cart::remove($request->rowId);
        $message = 'Item Removed from cart Successfully';
        $request->session()->flash('success', $message);
        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }

    public function checkout()
    {
        //if cart is empty redirect to cart page
        if (Cart::count() == 0) {
            return redirect()->route('front.cart');
        }

        //if user is not loged in redirect to login  page
        if (Auth::check() == false) {
            if (!session()->has('url.intended')) {
                session(['url.intended' => url()->current()]);
            }
            return redirect()->route('account.login');
        }
        session()->forget('url.intended');

        $customerAdderss = CustomerAddress::where('user_id',Auth::user()->id)->first();

        //calculate shipping
        if($customerAdderss != null){
            $userCountry = $customerAdderss->country_id;
            $shippingInfo = DB::table('shipping_charges')->where('country_id',$userCountry)->first();

            $totalQty = 0;
            $totalShippingCharge = 0;
            $grandTotal = 0;
            foreach(Cart::content() as $item){
                $totalQty += $item->qty;
            }

            $totalShippingCharge = $totalQty*$shippingInfo->amount;
            $grandTotal = cart::subtotal(2,'.','') + $totalShippingCharge ;
        }else{
            $grandTotal = cart::subtotal(2,'.','') ;
            $totalShippingCharge = 0;
        }




        $countries = Country::orderBy('name', 'ASC')->get();
        return view('front.checkout', compact('countries','customerAdderss','totalShippingCharge','grandTotal'));
    }

    public function processCheckout(Request $request)
    {

        //apply validation
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|min:3',
            'last_name' => 'required',
            'email' => 'required|email',
            'country' => 'required',
            'address' => 'required|min:30',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'mobile' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message'=>"please fix the errors",
                'error' => $validator->errors()
            ]);
        }

        //save user address
        $user = Auth::user();
       CustomerAddress::updateOrCreate(
            ['user_id'=>$user->id],
            [
                'user_id'=>$user->id,
                'first_name'=>$request->first_name,
                'last_name'=>$request->last_name,
                'email'=>$request->email,
                'mobile'=>$request->mobile,
                'country_id'=>$request->country,
                'address'=>$request->address,
                'appartment'=>$request->appartment,
                'city'=>$request->city,
                'state'=>$request->state,
                'zip'=>$request->zip,
            ]
        );

        //save in orders table
        if($request->payment_method == 'cod'){
            //calculate shipping
            $shipping = 0;
            $subtotal = Cart::subtotal(2,'.','');

            $shippingInfo =  Shipping::where('country_id',$request->country)->first();
            $totalQty = 0;
            foreach(Cart::content() as $item){
                $totalQty += $item->qty;
            }

           if($shippingInfo != null){
            $shipping = $totalQty*$shippingInfo->amount;
            $grandTotal = $subtotal + $shipping ;
           }else{
            $shippingInfo =  Shipping::where('country_id','rest_of_world')->first();
            $shipping = $totalQty*$shippingInfo->amount;
            $grandTotal = $subtotal + $shipping ;
           }


            $orders = new Order();
            $orders->sub_total = $subtotal;
            $orders->shipping = $shipping;
            $orders->grand_total = $grandTotal;


            $orders->user_id = $user->id;
            $orders->first_name = $request->first_name;
            $orders->last_name = $request->last_name;
            $orders->email = $request->email;
            $orders->mobile = $request->mobile;
            $orders->country_id = $request->country;
            $orders->address = $request->address;
            $orders->appartment = $request->appartment;
            $orders->city = $request->city;
            $orders->state = $request->state;
            $orders->zip = $request->zip;
            $orders->order_notes = $request->order_notes;
            $orders->save();


            //save in orders in  orders item table
            foreach(Cart::content() as $item){
                $orderItem =new OrderItem();
                $orderItem->product_id = $item->id;
                $orderItem->order_id = $orders->id;
                $orderItem->name = $item->name;
                $orderItem->qty = $item->qty;
                $orderItem->price = $item->price;
                $orderItem->total = $item->price*$item->qty;
                $orderItem->save();
            }
            $request->session()->flash('success','You have successfully placed your order');
            Cart::destroy();
            return response()->json([
                'status' => true,
                'orderId' => $orders->id,
                'message'=>"Order Saved Successfully",
            ]);

        }else{

        }
    }

    public function thanks($id){
        return view('front.thank',['id'=>$id]);
    }

    public function getOrderSummary(Request $request){

        $subTotal = Cart::subtotal('2','.','');

        if($request->country_id >0){
            $shippingInfo =  Shipping::where('country_id',$request->country_id)->first();
            $totalQty = 0;
            foreach(Cart::content() as $item){
                $totalQty += $item->qty;
            }

           if($shippingInfo != null){
            $shippingCharge = $totalQty*$shippingInfo->amount;
            $grandTotal = $subTotal + $shippingCharge ;

            return response()->json([
                'status'=>true,
                'grandTotal' => number_format($grandTotal,2),
                'shippingCharge' => number_format($shippingCharge,2)
            ]);
           }else{
            $shippingInfo =  Shipping::where('country_id','rest_of_world')->first();
            $shippingCharge = $totalQty*$shippingInfo->amount;
            $grandTotal = $subTotal + $shippingCharge ;

            return response()->json([
                'status'=>true,
                'grandTotal' =>number_format($grandTotal,2) ,
                'shippingCharge' =>  number_format($shippingCharge,2)
            ]);
           }
        }else{
            $shippingCharge = 0;
            $grandTotal = $subTotal + $shippingCharge ;
            return response()->json([
                'status'=>true,
                'grandTotal' =>number_format($grandTotal,2),
                'shippingCharge' =>number_format(0,2)
            ]);
        }
    }
}
