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
        $admin=Admin::where([
                    ['username', $request->username],
                ])->get();
        if (!($admin->isEmpty()))
        if(Hash::check($request->get('password'),$admin[0]->password)) {
            return response([
                'error' => false,
                'admin' => $admin
            ],200);
        } else return response([
            'message'=>'Tài khoản hoặc mật khẩu không đúng',
            ],500);
    }

    public function logout(){
        auth()->logout();
        return response()->json(['message' => 'Đã đăng xuất']);
    }
}