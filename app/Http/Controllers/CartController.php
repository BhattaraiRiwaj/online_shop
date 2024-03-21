<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
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
                $message = $products->title . " Added In cart";
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
            $message = $products->title . " Added In cart";
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
}
