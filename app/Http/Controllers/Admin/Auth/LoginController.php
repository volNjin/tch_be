<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Models\Admin;

class LoginController extends Controller{

    public function login(Request $request){
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::guard('admin')->attempt([
            'username' => $request->input('username'),
            'password' => $request->input('password')
        ])) {

            return response([
                'message'=>'success'
            ],200);
        }
        Session::flash('error', 'Username hoặc Password không đúng');
        return redirect()->back();
    }

    public function logout(){
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }
}