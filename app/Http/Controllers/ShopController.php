<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $categorySlug = null, $subCategorySlug = null)
    {

        // dd($request->all(),$categorySlug,$subCategorySlug);
        $categorySelected = '';
        $SubCategorySelected = '';
        $brandArray = [];
        $priceMax = (intval($request->get('price_max')) == 0) ? 1000 : $request->get('price_max');
        $priceMin = intval($request->get('price_min'));
        $sort = $request->get('sort');


        $categories = Category::orderBy('name', 'ASC')
            ->with('sub_category')
            ->orWhere('status', 1)
            ->get();
        $brands = Brand::orderBy('name', 'ASC')
            ->where('status', 1)
            ->get();

        $products = Product::where('status', 1);

        //Apply Filter here
        if (!empty($categorySlug)) {
            $category = Category::where('slug', $categorySlug)->first();
            $products = $products->where('category_id', $category->id);
            $categorySelected = $category->id;
        }

        if (!empty($subCategorySlug)) {
            $subCategory = SubCategory::where('slug', $subCategorySlug)->first();
            $products = $products->where('sub_category_id', $subCategory->id);
            $SubCategorySelected = $subCategory->id;
        }

        if (!empty($request->get('brand'))) {
            $brandArray = explode(',', $request->get('brand'));
            $products = $products->whereIn('brand_id', $brandArray);
        };

        if ($request->get('price_max') != '' && $request->get('price_min') != '') {

            if ($request->get('price_max') == 1000) {
                $products = $products->whereBetween('price', [intval($request->get('price_min')), 1000000]);
            } else {
                $products = $products->whereBetween('price', [intval($request->get('price_min')), intval($request->get('price_max'))]);
            }
        };


        if ($request->get('sort') != '') {
            if ($request->get('sort') == 'latest') {
                $products = $products->orderBy('id', 'DESC');
            } elseif ($request->get('sort') == 'price_asc') {
                $products = $products->orderBy('price', 'ASC');
            } else {
                $products = $products->orderBy('price', 'DESC');
            }
        } else {
            $products = $products->orderBy('id', 'DESC');
        };
        $products = $products->paginate(6);

        return view('front.shop', compact('categories', 'brands', 'products', 'categorySelected', 'SubCategorySelected', 'brandArray', 'priceMax', 'priceMin', 'sort'));
    }


    public function product($slug, Request $request)
    {
        $products = Product::where('slug', $slug)->with('product_images')->first();

        if ($products == null) {
            abort(404);
        }


        //Fetch Related Products
        $relatedProducts = [];
        if ($products->related_products != '') {
            $productArray = explode(',', $products->related_products);

            $relatedProducts =  Product::whereIn('id', $productArray)->get();
        }
        return view('front.product', compact('products','relatedProducts'));
    }
}
