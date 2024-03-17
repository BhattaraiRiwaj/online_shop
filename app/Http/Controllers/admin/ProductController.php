<?php

namespace App\Http\Controllers\admin;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\TempImage;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $products = Product::with('product_images')->latest('id');

        if(!empty($request->get('keyword'))){
            $products = $products
            ->where('products.title','like','%'.$request->get('keyword').'%')
            ->orWhere('products.slug','like','%'.$request->get('keyword').'%')
            ->orWhere('products.category_id','like','%'.$request->get('keyword').'%');
        }

        $products = $products->paginate(10);
        return view('admin.product.index',compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::orderBy('name','ASC')->get();
        $brands = Brand::orderBy('name','ASC')->get();
        return view('admin.product.create',compact('categories','brands'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $rules =  [
            'title' => 'required',
            'slug' => 'required|unique:products',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products,sku',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No',
       ];
       if(!empty($request->track_qty) && $request->track_qty == 'Yes'){
        $rules['qty'] = 'required|numeric';
       }
        $validator = Validator::make($request->all(),$rules);

        if ($validator->passes()){
            $products = new Product();
            $products->title = $request->title;
            $products->slug = $request->slug;
            $products->description = $request->description;
            $products->price = $request->price;
            $products->compare_price = $request->compare_price;
            $products->sku = $request->sku;
            $products->barcode = $request->barcode;
            $products->track_qty = $request->track_qty;
            $products->qty = $request->qty;
            $products->status = $request->status;
            $products->category_id  = $request->category;
            $products->sub_category_id  = $request->sub_category;
            $products->brand_id  = $request->brand;
            $products->is_featured = $request->is_featured;
            $products->save();

            //save gallary image
            if(!empty($request->image_array)){
                foreach($request->image_array as $temp_image_id){
                    $temp_image_info = TempImage::find($temp_image_id);
                    $extArray = explode('.',$temp_image_info->name);
                    $ext = last($extArray); //like jpg,png


                    // 1710437542.jpg
                    $productImage = new ProductImage();
                    $productImage->product_id = $products-> id;
                    $productImage->image = 'NULL';
                    $productImage->save();

                    $imageName = $products->id.'-'.$productImage->id.'-'.time().'.'.$ext;
                    echo $imageName;
                    $productImage->image = $imageName;
                    $productImage->save();

                    //generate product thumbnail
                    //large image
                    $sourcePath = public_path().'temp/'.$imageName;
                    $destPath = public_path().'/temp/products/largeImage/'.$imageName;
                    $image = Image::make($sourcePath);
                    $image->resize(1400, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $image->save($destPath);

                    //small image
                    $destPath = public_path().'/temp/products/smallImage/'.$imageName;
                    $image = Image::make($sourcePath);
                    $image->resize(300,300);
                    $image->save($destPath);
                }
            }

            $request->session()->flash('success','Product Added Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Product Added Successfully'
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function productStatus($id,Request $request){

        $product = Product::findOrFail($id);

        if(!empty($product)){
            if($product->status){
                $product->status = 0;
            }else{
                $product->status = 1;
            }
        }

        $product->save();

        $request->session()->flash('success','Product Status Changed Successfully.');

        return redirect()->back();
    }
}
