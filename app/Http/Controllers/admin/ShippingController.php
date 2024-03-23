<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Shipping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShippingController extends Controller
{
    public function create(){
        $countries = Country::get();

        $shippingCharges = Shipping::select('shipping_charges.*','countries.name')->leftJoin('countries','countries.id','shipping_charges.country_id')->get();
        return view('admin.shipping.create',compact('countries','shippingCharges'));
    }

    public function store(Request $request){
        $count = Shipping::where('country_id',$request->country)->count();

        if($count>0){
            $request->session()->flash('error','Shipping Already Exist');
            return response()->json([
                'status'=>true,
            ]);
        }

        $validator = Validator::make($request->all(),[
            'country'=>'required',
            'amount'=>'required'
        ]);

        if($validator->passes()){
            $shippingCharge = new Shipping();
            $shippingCharge->country_id = $request->country;
            $shippingCharge->amount = $request->amount;
            $shippingCharge->save();

            $request->session()->flash('success','Shipping Amount Added Successfully');
            return response()->json([
                'status'=>true,
                'message'=>'Shipping Amount Added Successfully'
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'error'=>$validator->errors()
            ]);
        }
    }

    public function edit($id,Request $request){
        $shippings = Shipping::find($id);

        $countries = Country::get();

        $shippingCharges = Shipping::select('shipping_charges.*','countries.name')
                                    ->leftJoin('countries','countries.id','shipping_charges.country_id')
                                    ->get();

        return view('admin.shipping.edit',compact('countries','shippingCharges','shippings'));
    }


    public function update(Request $request,$id){
        $shippingCharge = Shipping::find($id);

        if($shippingCharge == null){
            $request->session()->flash('error','Shipping Not Found');
            return redirect()->route('shipping.create');
        }
        $validator = Validator::make($request->all(),[
            'country'=>'required',
            'amount'=>'required'
        ]);

        if($validator->passes()){

            $shippingCharge->country_id = $request->country;
            $shippingCharge->amount = $request->amount;
            $shippingCharge->save();

            $request->session()->flash('success','Shipping Amount Updated Successfully');
            return response()->json([
                'status'=>true,
                'message'=>'Shipping Amount Updated Successfully'
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'error'=>$validator->errors()
            ]);
        }

    }

    public function destroy($id,Request $request){

        $shippingCharge = Shipping::find($id);
        // dd($id);
        if($shippingCharge == null){
            $request->session()->flash('error','Shipping Not Found');
            return redirect()->route('shipping.create');
        }

        $shippingCharge->delete();

        $request->session()->flash('success','Shipping Deleted Successfully');
            return response()->json([
                'status'=>true,
                'message'=>'Shipping Deleted Successfully'
            ]);
    }
}
