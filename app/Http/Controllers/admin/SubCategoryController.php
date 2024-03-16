<?php

namespace App\Http\Controllers\admin;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Symfony\Contracts\Service\Attribute\Required;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $subCategories = SubCategory::select('sub_categories.*','categories.name as categoryName')
                        ->latest('sub_categories.id')
                        ->leftJoin('categories','categories.id','sub_categories.category_id');

        if(!empty($request->get('keyword'))){
            $subCategories = $subCategories
            ->where('sub_categories.name','like','%'.$request->get('keyword').'%')
            ->orWhere('categories.name','like','%'.$request->get('keyword').'%');;
        }

        $subCategories = $subCategories->paginate(10);
        return view('admin.sub_category.index',compact('subCategories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::orderBy('name','ASC')->get();
        return view('admin.sub_category.create',compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validotor = Validator::make($request->all(),[
            'category' => "required",
            'name' => "required",
            'slug' => "required|unique:sub_categories",
            'status' => "required",
        ]);

        if($validotor->passes()){
            $subCategory = new SubCategory();
            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->category_id = $request->category;
            $subCategory->save();

            $request->session()->flash('success','Sub Category Created Successfully');

            return response()->json([
                'status' => true,
                'message'=> "Sub Category Created Successfully"
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'errors'=>$validotor->errors()
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
        $categories = Category::orderBy('name','ASC')->get();

        $subCategories = SubCategory::find($id);

        return view('admin.sub_category.edit',compact('categories','subCategories'));
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
        $subCategory = SubCategory::find($id);

        $validotor = Validator::make($request->all(),[
            'category' => "required",
            'name' => "required",
            'slug' => "required|unique:sub_categories,slug,'.$subCategory->id.',id",
            'status' => "required",
        ]);


        if($validotor->passes()){
            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->category_id = $request->category;
            $subCategory->save();

            $request->session()->flash('success','Sub Category Updated Successfully');

            return response()->json([
                'status' => true,
                'message'=> "Sub Category Updated Successfully"
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'errors'=>$validotor->errors()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,Request $request)
    {
        $subCategory = SubCategory::find($id);

        if(empty($subCategory)){
            // return redirect()->route('categories.index');
            $request->session()->flash('error','Sub Category Not Found');
            return response()->json([
                'status' => true,
                'message' => 'Sub Category Not Found'
            ]);
        }

        //delete old image
        // File::delete(public_path().'/uploads/category/thumb/'.$subCategory->image );
        // File::delete(public_path().'/uploads/category/'.$subCategory->image );

        $subCategory->delete();

        $request->session()->flash('success','Sub Category deleted successfully');

        return response()->json([
            'status' => true,
            'message' => 'Sub Category deleted successfully'
        ]);
    }

    public function userStatus($id,Request $request){

        $subCategory = SubCategory::findOrFail($id);

        if(!empty($subCategory)){
            if($subCategory->status){
                $subCategory->status = 0;
            }else{
                $subCategory->status = 1;
            }
        }
        $subCategory->save();

        $request->session()->flash('success','User Status Changed Successfully.');

        return redirect()->back();
    }
}
