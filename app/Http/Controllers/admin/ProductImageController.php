<?php

namespace App\Http\Controllers\admin;

use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class ProductImageController extends Controller
{
    public function update(Request $request)
    {
        $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $sourcePath = $image->getPathName();

        $productImage = new ProductImage();
        $productImage->product_id = $request->product_id;
        $productImage->image = 'NULL';
        $productImage->save();


        $imageName = $request->product_id.'-'.$productImage->id.'-'.time().'.'.$ext;
        $productImage->image = $imageName;
        $productImage->save();

        //generate product thumbnail
        //large image
        $destPath = public_path() . '/temp/products/largeImage/' . $imageName;

        $image = Image::make($sourcePath);

        $image->resize(1400, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $image->save($destPath);

        //small image
        $destPath = public_path() . '/temp/products/smallImage/' . $imageName;
        $image = Image::make($sourcePath);
        $image->fit(300, 300);
        $image->save($destPath);

        return response()->json([
            'status'=>true,
            'image_id'=> $productImage->id,
            'ImagePath'=> asset("temp/products/smallImage/".$productImage->image),
            'message' => 'Image Saved Successfully',
        ]);
    }


    public function destroy(Request $request){
        $productImage = ProductImage::find($request->id);

        // dd($productImage);
        if(empty($productImage)){
            return response()->json([
                'status'=>false,
                'message' => 'Product Image Not Found',
            ]);
        }

        //delete images from delete
        File::delete(public_path('/temp/products/largeImage/'.$productImage->image));
        File::delete(public_path('/temp/products/smallImage/'.$productImage->image));

        $productImage->delete();

        return response()->json([
            'status'=>true,
            'message' => 'Image Deleted Successfully',
        ]);
    }
}
