<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCoupon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class DiscountCodeController extends Controller
{
    public function index(Request $request)
    {
        $discounts = DiscountCoupon::orderBy('id','ASC');

        if (!empty($request->get('keyword'))) {
            $discounts = $discounts
                ->where('discount_coupons.code', 'like', '%' . $request->get('keyword') . '%')
                ->orWhere('discount_coupons.name', 'like', '%' . $request->get('keyword') . '%');
        }
        $discounts = $discounts->paginate(10);
        return view('admin.discount.index',compact('discounts'));
    }


    public function create()
    {
        return view('admin.discount.create');
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'code'=>'required',
            'type'=>'required',
            'discount_amount'=>'required|numeric',
            'min_amount'=>'required',
            'status'=>'required',
        ]);

        if($validator->passes()){
            //starting date must be greater than current date
            if($request->start_at != null){
                $now = Carbon::now();
                $startAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->start_at);

                if($startAt->lte($now)==true){
                    return response()->json([
                        'status'=>false,
                        'errors'=> ['start_at'=>'starting date cannot  be less then current date time']
                    ]);
                }
            }
            //expired date must be greater than start date
            if($request->start_at != null && $request->expires_at != null ){
                $startAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->start_at);
                $expiresAt= Carbon::createFromFormat('Y-m-d H:i:s',$request->expires_at);

                if($expiresAt->gt($startAt)==false){
                    return response()->json([
                        'status'=>false,
                        'errors'=> ['expires_at'=>'Expiry date must  be greater then starting date time']
                    ]);
                }
            }

            $discountCoupons = new DiscountCoupon();
            $discountCoupons->code = $request->code;
            $discountCoupons->name = $request->name;
            $discountCoupons->description = $request->description;
            $discountCoupons->max_uses = $request->max_uses;
            $discountCoupons->max_uses_user = $request->max_uses_user;
            $discountCoupons->type = $request->type;
            $discountCoupons->discount_amount = $request->discount_amount;
            $discountCoupons->min_amount = $request->min_amount;
            $discountCoupons->status = $request->status;
            $discountCoupons->start_at = $request->start_at;
            $discountCoupons->expires_at = $request->expires_at;
            $discountCoupons->save();

            $request->session()->flash('success','Discount coupon Created Successfully');

            return response()->json([
                'status'=>true,
                'message'=> 'Discount coupon Created Successfully'
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'errors'=> $validator->errors()
            ]);
        }
    }


    public function edit($id,Request $request)
    {
        $discounts = DiscountCoupon::find($id);

        if($discounts == null){
            $request->session()->flash('error','Discount coupon not found');
            return response()->json([
                'status'=>false,
                'message'=>'Discount coupon not found'
            ]);
        }

        return view('admin.discount.edit',compact('discounts'));
    }


    public function update(Request $request, $id)
    {
        $discountCoupons = DiscountCoupon::findOrFail($id);

        $validator = Validator::make($request->all(),[
            'code'=>'required',
            'type'=>'required',
            'discount_amount'=>'required|numeric',
            'min_amount'=>'required',
            'status'=>'required',
        ]);

        if($validator->passes()){

            //expired date must be greater than start date
            if($request->start_at != null && $request->expires_at != null ){
                $startAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->start_at);
                $expiresAt= Carbon::createFromFormat('Y-m-d H:i:s',$request->expires_at);

                if($expiresAt->gt($startAt)==false){
                    return response()->json([
                        'status'=>false,
                        'errors'=> ['expires_at'=>'Expiry date must  be greater then starting date time']
                    ]);
                }
            }
            $discountCoupons->code = $request->code;
            $discountCoupons->name = $request->name;
            $discountCoupons->description = $request->description;
            $discountCoupons->max_uses = $request->max_uses;
            $discountCoupons->max_uses_user = $request->max_uses_user;
            $discountCoupons->type = $request->type;
            $discountCoupons->discount_amount = $request->discount_amount;
            $discountCoupons->min_amount = $request->min_amount;
            $discountCoupons->status = $request->status;
            $discountCoupons->start_at = $request->start_at;
            $discountCoupons->expires_at = $request->expires_at;
            $discountCoupons->save();

            $request->session()->flash('success','Discount coupon Updated Successfully');

            return response()->json([
                'status'=>true,
                'message'=> 'Discount coupon Updated Successfully'
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'errors'=> $validator->errors()
            ]);
        }
    }


    public function destroy($id,Request $request)
    {
        $discounts = DiscountCoupon::findOrFail($id);

        if($discounts == null){
            Session()->flash('error','Discount coupon not found');
            return response()->json([
                'status'=>true,
            ]);
        }
        $discounts->delete();

        $request->session()->flash('success','Discount Deleted Successfully');
        return response()->json([
            'status'=>true,
        ]);
    }

    public function discountStatus($id){

        $discount = DiscountCoupon::findOrFail($id);

        if(!empty($discount)){
            if($discount->status){
                $discount->status = 0;
            }else{
                $discount->status = 1;
            }
        }

        $discount->save();
        return redirect()->back()->with('success','Discount Coupon Status Changed Successfully.');
    }
}
