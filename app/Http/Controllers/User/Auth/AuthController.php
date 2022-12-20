<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller{
    public function getRegister(){
        return view('user.register');
    }

    public function register(Request $request){
        try{
            $this->validate($request, array(
                'email' =>  'required|unique:users|email|max:255',
                'name' =>  'required|alpha_dash|max:20',
                'password' =>  'required|min:8',
                'confirm_password' => 'required|same:password'
            ));

            $user=User::create([
                'name'=> $request->input('name'),
                'email'=> $request->input('email'),
                'password'=>Hash::make($request->input('password'))
            ]);

            return response([
                'message'=>'success',
                'user'=>$user,
            ], 200);
        } catch(\Exception $exception){
            return response([
                'message' => $exception->getMessage()
            ], 400); 
    }
    }

    public function login(Request $request){

        $this->validate($request, [
            'email' =>  'required|email:filter',
            'password'  =>  'required',
        ]);


        if (Auth::attempt([
            'email' => $request->input('email'),
            'password' => $request->input('password')
        ], $request->has('remember')
        )) {
            return response([
                'message'=>'success',
                //'user'=>$user,
            ], 200);
        }

        Session::flash('error', 'Email hoặc Password không đúng');
        return redirect()->back();

    }


    public function getLogout(){
        Auth::logout();

        return redirect()->back();
    }
}