<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Cart;

class ShopController extends Controller
{
    public function index(Request $request)
    {
      //sortiranje proizvoda i broj proizvoda po stranici
        $page = $request->query("page");
        $size = $request->query("size");
        if(!$page)
           $page = 1;
        if(!$size)
           $size = 12;
        $order = $request->query("order");
        if(!$order)
            $order = -1;
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
            case 1:
                $o_column = "regular_price";
                $o_order = "ASC";
                break;
            case 1:
                $o_column = "regular_price";
                $o_order = "DESC";
                break;    
            default:
                $o_column = "id";
                $o_order = "DESC";
                    
        }
      //filtriranje Brendova
        $brands = Brand::orderBy('name', 'ASC')->get();
        $q_brands = $request->query("brands");

        $categories = Category::orderBy("name", 'ASC')->get();
        $q_categories = $request->query("categories");

        $products = Product::where(function($query) use($q_brands){
                               $query->whereIn('brand_id',explode(',',$q_brands))->orWhereRaw("'".$q_brands."'=''");
                            })
                            ->where(function($query) use($q_categories){
                                $query->whereIn('category_id',explode(',', $q_categories))->orWhereRaw("'".$q_categories."'=''");
                            })
                  ->orderBy('created_at','DESC')->orderBy($o_column, $o_order)->paginate($size);

        return view('shop',['products'=>$products, 'page'=>$page, 'size'=>$size, 'order'=>$order, 
        'brands'=>$brands, 'q_brands'=>$q_brands, 'categories'=>$categories, 'q_categories'=>$q_categories]);

    }

    public function productDetails($slug)
    {   
        //print_r($slug);
        $product = Product::where('slug', $slug)->first();
        $rproducts = Product::where('slug', '!=', $slug)->inRandomOrder('id')->get()->take(8);  
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
