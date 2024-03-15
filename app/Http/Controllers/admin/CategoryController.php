<?php

namespace App\Http\Controllers\admin;

use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redis;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = Category::latest()->first();

        if(!empty($request->get('keyword'))){
            $categories = $categories->where('name','like','%'.$request->get('keyword').'%');
        }
        $categories = $categories->paginate(10);

        return view('admin.category.index',compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
     $validator = Validator::make($request->all(),[
        'name'=>'required',
        'slug'=>'required|unique',
     ]);

     if($validator->passes()){
        $category =new Category();
        $category->name = $request->name;
        $category->slug = $request->slug;
        $category->status = $request->status;
        $category->save();

        //save image here
        if(!empty($request->image_id)){
            $tempImage = TempImage::find($request->image_id);
            $extArray = explode('.',$tempImage->name);
            $ext = last($extArray);
            $newImageName = $category->id. '.' .$ext;
            $sourcePath = public_path().'/temp/'.$tempImage->name;
            $destPath = public_path().'/uploads/category/'.$newImageName;
            File::copy($sourcePath,$destPath );

            //generate image thumbnail
            $destPath = public_path().'/uploads/category/thumb/'.$newImageName;
            $img = Image::make($sourcePath);
            $img->resize(450, 600);
            $img->save($destPath);

            $category->image = $newImageName;
            $category->save();
        }

        $request->session()->flash('success','category Added Successfully');
        return response()->json([
            'status'=>true,
            'message'=>"category Added Successfully"
        ]);

     }else{
        return response()->json([
            'status'=>false,
            'errors'=>$validator->errors()
        ]);
     }

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::find($id);

        if(empty($category)){
            return redirect()->route('categories.index');
        }

        return view('admin.category.edit',compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $category = Category::find($id);
        // dd($category->id);
        if(empty($category)){
            $request->session()->flash('error','category not  found');
            return response()->json([
                'status'=> false,
                'notFound'=> true,
                'message'=> 'Category Not Found',
            ]);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$category->id.',id',
        ]);

        // dd($validator);
        if($validator->passes()){
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();

            $oldImage = $category->image;
            //save image here
            if(!empty($request->image_id)){
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id.'-'.time(). '.' .$ext;

                $sourcePath = public_path().'/temp/'.$tempImage->name;
                $destPath = public_path().'/uploads/category/'.$newImageName;
                File::copy($sourcePath,$destPath );

                //generate image thumbnail
                $destPath = public_path().'/uploads/category/thumb/'.$newImageName;
                $img = Image::make($sourcePath);
                $img->fit(450, 600, function ($constraint) {
                    $constraint->upsize();
                });
                $img->save($destPath);

                $category->image = $newImageName;
                $category->save();

                //delete old image
                File::delete(public_path().'/uploads/category/thumb/'.$oldImage );
                File::delete(public_path().'/uploads/category/'.$oldImage );

            }

            $request->session()->flash('success','category Updated Successfully');

            return response()->json([
                'status'=>true,
                'message'=>"category Updated Successfully"
            ]);

         }else{
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
         }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,Request $request)
    {
        $category = Category::find($id);

        if(empty($category)){
            // return redirect()->route('categories.index');
            $request->session()->flash('error','Category Not Found');
            return response()->json([
                'status' => true,
                'message' => 'Category Not Found'
            ]);
        }

        //delete old image
        File::delete(public_path().'/uploads/category/thumb/'.$category->image );
        File::delete(public_path().'/uploads/category/'.$category->image );

        $category->delete();

        $request->session()->flash('success','Category deleted successfully');

        return response()->json([
            'status' => true,
            'message' => 'Category deleted successfully'
        ]);
    }


    public function userStatus($id,Request $request){

        $category = Category::findOrFail($id);

        if(!empty($category)){
            if($category->status){
                $category->status = 0;
            }else{
                $category->status = 1;
            }
        }

        $category->save();
        return redirect()->back()->with('success','User Status Changed Successfully.');
    }
}
