<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;


class WishlistController extends Controller
{

    public function addProductToWishlist(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $product = Product::findOrFail($validated['id']);
        $price = $product->sale_price ?: $product->regular_price;

        Cart::instance("wishlist")->add($product->id, $product->name, 1, $price)->associate('App\Models\Product');
        return response()->json(['status'=>200, 'message'=>'Success ! item successfully added to your wishlist.']);

    }

    
    public function getWishlistedProducts()
    {
        $items = Cart::instance("wishlist")->content();
        return view('wishlist', ['items'=>$items]);
    }


    public function removeProductFromWishlist(Request $request)
    {
        $validated = $request->validate([
            'rowId' => ['required', 'string'],
        ]);

        $rowId = $validated['rowId'];
        Cart::instance('wishlist')->remove($rowId);
        return redirect()->route('wishlist.list');
    }

    public function clearWishlist()
    {
        Cart::instance('wishlist')->destroy();
        return redirect()->route('wishlist.list');
    }

    public function moveToCart(Request $request)
    {
        $validated = $request->validate([
            'rowId' => ['required', 'string'],
        ]);

        $item = Cart::instance('wishlist')->get($validated['rowId']);
        if (!$item || !$item->model) {
            return redirect()->route('wishlist.list')->with('error', 'Item not found in wishlist.');
        }

        $product = Product::find($item->model->id);
        if (!$product) {
            Cart::instance('wishlist')->remove($validated['rowId']);
            return redirect()->route('wishlist.list')->with('error', 'Product is no longer available.');
        }

        $price = $product->sale_price ?: $product->regular_price;

        Cart::instance('wishlist')->remove($validated['rowId']);
        Cart::instance('cart')->add($product->id, $product->name, 1, $price)
                                  ->associate('App\Models\Product');
        return redirect()->route('wishlist.list');                          
    }

}
