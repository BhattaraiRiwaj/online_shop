<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminLoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.login');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function auhenticate(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required'
        ]);

        if($validator->passes()){
            if(Auth::guard('admin')->attempt(['email'=>$request->email,'password'=>
            $request->password],$request->get('remember'))){

                $admin = Auth::guard('admin')->user();
                if($admin->role == 1){
                    return redirect()->route('admin.dashboard');
                }else{
                    Auth::guard('admin')->logout();
                    return redirect()->route('admin.login')->with('error','You are not authorize to access this admin pannel.');
                }

            }else{
                return redirect()->route('admin.login')->with('error','Email/Password is incorrect.');
            };
        }else{
            return redirect()->route('admin.login')
                             ->withErrors($validator)
                             ->withInput($request->only('email'));
        }
    }
}
