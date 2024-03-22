<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        return view('front.account.login');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        return view('front.account.register');
    }


    public function processRegister(Request $request){
        $validator = Validator::make($request->all(),[
            'name'=>'required|min:3',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|min:5|confirmed',
        ]);

        if($validator->passes()){
            $users = new User();
            $users->name = $request->name;
            $users->email = $request->email;
            $users->phone = $request->phone;
            $users->password = Hash::make($request->password);
            $users->save();

            $request->session()->flash('success','You have been registered successfully');
            return response()->json([
                'status'=>true,
                'message' => 'You have been registered successfully'
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }
    }

    public function authenticate(Request $request){

        // dd($request->all(),$request->get('remember'));
        $validator = Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=> 'required'
        ]);

        if($validator->passes()){
            if(Auth::attempt(['email'=>$request->email,'password'=>$request->password],$request->get('remember'))){
                if(!session()->has('url.intended')){
                    return redirect(session()->get('url.intended'));
                }
                return redirect()->route('account.profile');
            }else{
                $request->session()->flash('error','Either Email/Password is In-correct');
                return redirect()->route('account.login')->withInput($request->only('email'));
            }
        }else{
            return redirect()->route('account.login')
            ->withErrors($validator)
            ->withInput($request->only('email'));
        }
    }

    public function profile(){
        return view('front.account.profile');
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('account.login')->with('success','you are Successfully Logout ');
    }
}
