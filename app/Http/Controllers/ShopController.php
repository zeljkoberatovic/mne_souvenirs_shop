<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Gloudemans\Shoppingcart\Facades\Cart;

class ShopController extends Controller
{
    public function index(Request $request)
    {
      //sortiranje proizvoda i broj proizvoda po stranici
        $page = max(1, (int) $request->query("page", 1));
        $size = (int) $request->query("size", 12);
        $size = in_array($size, [12, 24, 52, 100], true) ? $size : 12;
        $order = (int) $request->query("order", -1);
        $o_column = "";
        $o_order = "";
        switch ($order) {
            case 1:
                $o_column = "created_at";
                $o_order = "DESC";
                break;
            case 2:
                $o_column = "created_at";
                $o_order = "ASC";
                break;
            case 3:
                $o_column = "regular_price";
                $o_order = "ASC";
                break;
            case 4:
                $o_column = "regular_price";
                $o_order = "DESC";
                break;    
            default:
                $o_column = "id";
                $o_order = "DESC";
                    
        }
      //filtriranje brendova i kategorija uz sanitizaciju ID vrednosti
        $brands = Brand::orderBy('name', 'ASC')->get();
        $q_brands = trim((string) $request->query("brands", ""));
        $brandIds = array_values(array_filter(array_map('intval', explode(',', $q_brands)), fn($id) => $id > 0));

        $categories = Category::orderBy("name", 'ASC')->get();
        $q_categories = trim((string) $request->query("categories", ""));
        $categoryIds = array_values(array_filter(array_map('intval', explode(',', $q_categories)), fn($id) => $id > 0));

        $products = Product::query()
            ->with(['brand', 'category'])
            ->when(!empty($brandIds), function ($query) use ($brandIds) {
                $query->whereIn('brand_id', $brandIds);
            })
            ->when(!empty($categoryIds), function ($query) use ($categoryIds) {
                $query->whereIn('category_id', $categoryIds);
            })
            ->orderBy($o_column, $o_order)
            ->paginate($size);

        return view('shop',['products'=>$products, 'page'=>$page, 'size'=>$size, 'order'=>$order, 
        'brands'=>$brands, 'q_brands'=>$q_brands, 'categories'=>$categories, 'q_categories'=>$q_categories]);

    }

    public function productDetails($slug)
    {   
        $product = Product::where('slug', $slug)->firstOrFail();
        $rproducts = Product::where('slug', '!=', $slug)->inRandomOrder()->limit(8)->get();
        return view('details', ['product' =>$product, 'rproducts'=>$rproducts]);
    }

    //funkcija za azuriranje broja artikala u wishlist-i
    public function getCartAndWishlistCount()
    {
        $cartCount = Cart::instance("cart")->content()->count();
        $wishlistCount = Cart::instance("wishlist")->content()->count();
        return response()->json(['status'=>200, 'cartCount'=>$cartCount, 'wishlistCount'=>$wishlistCount]);
    }

}
