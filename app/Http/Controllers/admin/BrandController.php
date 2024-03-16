<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $brands = Brand::latest();

        if(!empty($request->get('keyword'))){
            $brands = $brands->where('name','like','%'.$request->get('keyword').'%')
                             ->orWhere('slug','like','%'.$request->get('keyword').'%');
        }
        $brands = $brands->paginate(10);

        return view('admin.brand.index',compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.brand.create');
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
            'slug'=>'required|unique:brands,slug',
         ]);

         if($validator->passes()){
            $brand =new Brand();
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();

            $request->session()->flash('success','Brand Added Successfully');
            return response()->json([
                'status'=>true,
                'message'=>"Brand Added Successfully"
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
        $brand = Brand::find($id);

        if(empty($brand)){
            return redirect()->route('brands.index');
        }

        return view('admin.brand.edit',compact('brand'));
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
        $brand = Brand::find($id);

         if(empty($brand)){
            $request->session()->flash('error','Brand not  Found');
            return response()->json([
                'status'=> false,
                'notFound'=> true,
                'message'=> 'Brand Not Found',
            ]);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,'.$brand->id.',id',
        ]);

        if($validator->passes()){
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();

            $request->session()->flash('success','Brand Updated Successfully');

            return response()->json([
                'status'=>true,
                'message'=>"Brand Updated Successfully"
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,Request $request)
    {
        $brand = Brand::find($id);

        if(empty($brand)){

            $request->session()->flash('error','Brand Not Found');
            return response()->json([
                'status' => true,
                'message' => 'Brand Not Found'
            ]);
        }

        $brand->delete();

        $request->session()->flash('success','Brand deleted successfully');

        return response()->json([
            'status' => true,
            'message' => 'Brand deleted successfully'
        ]);
    }


    public function brandStatus($id,Request $request)
    {
        $brand = Brand::findOrFail($id);

        if(!empty($brand)){
            if($brand->status){
                $brand->status = 0;
            }else{
                $brand->status = 1;
            }
        }

        $brand->save();
        return redirect()->back()->with('success','Brand Status Changed Successfully');
    }
}
