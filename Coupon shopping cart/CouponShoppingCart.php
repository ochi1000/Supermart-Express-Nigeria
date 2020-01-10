<?php

namespace App\Controllers;

use App\Classes\CSRFToken;
use App\Classes\Request;
use App\Classes\Cart;
use App\Classes\Session;
use App\Models\Product;
use Exception;

class CartController extends BaseController{
    protected $cartTotal;
    protected $checkout = false;

    //Show Cart Page
    public function show(){
        return view('/cart');
    }
    
    /**
     * Item variable uses Product model
     * Function to get cart items
     */
    public function getCartItems(){
        try {
            $result = array();
            $this->cartTotal = 0;
            if(!Session::has('user_cart') || count(Session::get('user_cart')) < 1){
                echo json_encode(['fail' => 'No item in cart']);
                exit;
            }
            $index = 0;
            foreach($_SESSION['user_cart'] as $cart_items){
                $productId = $cart_items['product_id'];
                $quantity = $cart_items['quantity'];
                $item = Product::where('id', $productId)->first();

                //Delivery Fee
                $deliveryFee = 50.00;
                //Net Total
                $totalPrice = $item->price * $quantity;
                //Delivery Fee total
                $totalDeliveryFee = $deliveryFee * $quantity;
                //Gross Total
                $this->cartTotal = $totalPrice + $totalDeliveryFee + $this->cartTotal;

                array_push($result, [
                    'id' => $item->id,
                    'name' => $item->name,
                    'image' => $item->image_path,
                    'description' => $item->description,
                    'price' => $item->price,
                    'total' => $totalPrice,
                    'quantity' => $quantity,
                    'stock' => $item->quantity,
                    'index' => $index,                                  
                ]);
                $index++;
            }
            exit;
        } catch (\Exception $ex) {
            //log into database or send to admin
        }
    }

    //Add Item function, uses Cart Controller Class
    public function addItem(Request $request)
    {
        if($request->has('post')){
            $item = $request->post;
            if(CSRFToken::verifyCSRFToken($item->token, false)){
                if(!$item->product_id){
                    throw new \Exception('Malicious Activity');
                }
            }
            Cart::add($item);
            echo("Product Added to Cart Successfully"); 
            exit;
        }
    }
    
    //Remove cart item
    public function removeItem(Request $request){
        if(!$request->item_index === ''){
            throw new \Exception('Malicious Activity');
        }
        // remove item
        Cart::removeItem($request->item_index);
        echo('Product Removed From Cart');
        exit;
    }

    //Clear Cart
    public function clearCart(Request $request){
        Cart::clear($request);
        echo ('Done!!');
        exit;
        
    }

    /**Checkout is made with a gateway plugin
     * In this case, i made something simple
     */
    public function checkout(Request $request){
        $paidAmount = $request->paidAmount;
        if($paidAmount < $this->cartTotal){
            echo('Insufficient funds');
        }elseif($paidAmount > $this->cartTotal){
            $balance = $paidAmount - $this->cartTotal;
            return $balance;
            echo('Transaction completed');
        }else{
            echo('Transaction completed');
        }
        $this->checkout = true;
        Cart::clear($request);
    }

    /**Supermart dev coupon
     * i made a custom coupon = EV2BTR97
     * Coupon reduces by the $cartTotal by 1000
     */
    public function superMartCoupon(Request $request){
        $couponCode = 'EV2BTR97';
        $inputCoupon = $request->coupon;
        if($couponCode == $inputCoupon){
            $this->cartTotal = $this->cartTotal - 1000 ;
            return $this->cartTotal;
        }else{
            echo('Invalid coupon code');
        }
        return $this->cartTotal;
    }
}