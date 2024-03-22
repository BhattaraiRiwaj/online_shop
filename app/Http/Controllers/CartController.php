<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Gloudemans\Shoppingcart\Facades\Cart;

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
                $message = '<strong>'.$products->title.'</strong> Added In Your Cart Successfully';
                $request->session()->flash('success',$message);
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
            $message = '<strong>'.$products->title.'</strong> Added In Your Cart Successfully';
            $request->session()->flash('success',$message);
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
        return view('front.cart',compact('carts'));
    }


    public function updateCart(Request $request){

        $rowId = $request->rowId;
        $qty = $request->qty;

        $itemInfo = Cart::get($rowId);

        $product = Product::find($itemInfo->id);
        // dd($product);
        //check qty available in stock
        if($product->track_qty == "Yes"){
            if($qty <= $product->qty){
                Cart::update($rowId, $qty);
                $status = true;
                $message = "cart updated successfully";
                $request->session()->flash('success',$message);
            }else{
                $status = false;
                $message ="Requested qty('$qty') is not available in stock.";
                $request->session()->flash('error',$message);
            }

        }else{
            Cart::update($rowId, $qty);
            $status = true;
            $message ="cart updated successfully";
            $request->session()->flash('success',$message);
        }


        return response()->json([
            'status'=> $status,
            'message'=>$message
        ]);
    }

    public function deleteItem(Request $request){

        $itemInfo = Cart::get($request->rowId);
        if($itemInfo == null){
            $errorMessage = 'Item Not found in Cart';
            $request->session()->flash('error',$errorMessage);
            return response()->json([
                'status'=> false,
                'message'=> $errorMessage
            ]);
        }

        Cart::remove($request->rowId);
        $message = 'Item Removed from cart Successfully';
        $request->session()->flash('success',$message);
        return response()->json([
            'status'=> true,
            'message'=> $message
        ]);

    }

    public function checkout(){
        //if cart is empty redirect to cart page
        if(Cart::count() == 0){
            return redirect()->route('front.cart');
        }

        //if user is not loged in redirect to login  page
        if(Auth::check()== false){
            if(!session()->has('url.intended')){
                session(['url.intended'=>url()->current()]);
            }
            return redirect()->route('account.login');
        }

        $countries = Country::orderBy('name','ASC')->get();
        session()->forget('url.intended');
        return view('front.checkout',compact('countries'));
    }
}
