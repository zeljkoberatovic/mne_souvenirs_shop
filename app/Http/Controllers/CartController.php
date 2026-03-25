<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;


class CartController extends Controller
{
    public function index()
    {
        //preuzimanje sadrzaja iz korpe
        $cartItems = Cart::instance('cart')->content();
        return view('cart',['cartItems' => $cartItems]);
    }
    //dodavanje sadrzaja u corpi
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $product = Product::findOrFail($validated['id']);
        $price = $product->sale_price ? $product->sale_price : $product->regular_price;
        Cart::instance('cart')->add($product->id, $product->name, $validated['quantity'], $price)->associate('App\Models\Product');
        return redirect()->back()->with('message','Success ! Item has been added successfully!');
    }

    //update cart Quantity
    public function updateCart(Request $request)
    {
        $validated = $request->validate([
            'rowId' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        Cart::instance('cart')->update($validated['rowId'], $validated['quantity']);
        return redirect()->route('cart.index');
    }
    
    public function removeItem(Request $request)
    {
        $validated = $request->validate([
            'rowId' => ['required', 'string'],
        ]);

        $rowId = $validated['rowId'];
        Cart::instance('cart')->remove($rowId);
        return redirect()->route('cart.index');
    }

    public function clearCart()
    {
        Cart::instance('cart')->destroy();
        return redirect()->route('cart.index');
    }
}

