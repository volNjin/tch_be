<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller{
    public function login(Request $request){
        try{
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
                    'message'=>'Đăng nhập thành công',
                    'user'=>auth()->user(),
                ], 200);
            } else return response([
                'message'=>'Tài khoản hoặc mật khẩu không đúng',
            ],500);
        }catch(\Exception $exception){
            return response([
                'message' => $exception->getMessage()
            ], 500); 
        } 
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

    public function logout(){
        Auth::logout();
        return response()->json(['message' => 'Đã đăng xuất']);
    }

    public function changePassword(Request $request){
        $this->validate($request, [
            'old_password'=>'required|min:8|max:100',
            'new_password'=>'required|min:8|max:100',
            'confirm_password'=>'required|same:new_password'
        ]);
        $password = User::find(auth()->user()->id);
        $checked=Hash::check($request->old_password,$password->password);
        if (!$checked) {
            // The passwords matches
            return response([
                "error"=>"Mật khẩu không đúng. Vui lòng nhập lại"
            ]);
        }

        if(strcmp($request->get('old_password'), $request->get('new_password')) == 0){
            //Current password and new password are same
            return response([
                "error"=>"Mật khẩu mới không được trùng với mật khẩu cũ. Vui lòng nhập lại"
            ]);
        }
        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make(($request->new_pasword))
        ]);

        return response([
            'message'=>'Đổi mật khẩu thành công',
            'user'=>auth()->user(),
        ], 200);
    }
}