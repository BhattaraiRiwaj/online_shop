<?php

namespace App\Http\Controllers\admin;

use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
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
        $categories = Category::latest();

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
        'slug'=>'required|unique:categories,slug',
     ]);

     if($validator->passes()){
        $category =new Category();
        $category->name = $request->name;
        $category->slug = $request->slug;
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

        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'slug'=>'required'
        ]);

        $input = $request->all();

        if($validator->passes()){
            $category->update($input);
            return redirect()->route('categories.index')->with('success','Category Updated successfully.');
        }
        return redirect()->back()->with('error','Categories field reuired.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::find($id);

        $category->delete();

        return redirect()->back()->with('success','Category deleted successfully.');

    }

    public function userStatus($id,Request $request){
        $category = Category::findOrFail($id);

        if($category){
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
