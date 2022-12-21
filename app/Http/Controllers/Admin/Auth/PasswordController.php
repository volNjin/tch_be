<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
class PasswordController extends Controller{

    public function update(Request $request){
        $this->validate($request, [
            'old_password'=>'required|min:8|max:100',
            'new_password'=>'required|min:8|max:100',
            'confirm_password'=>'required|same:new_password'
        ]);

        $user = Auth::user();

        if (!(Hash::check($request->old_password,$user->password))) {
            // The passwords matches
            return redirect()->back()->with("error","Mật khẩu không đúng. Vui lòng nhập lại");
        }

        if(strcmp($request->get('old_password'), $request->get('new_password')) == 0){
            //Current password and new password are same
            return redirect()->back()->with("error","Mật khẩu mới không được trùng với mật khẩu cũ. Vui lòng nhập lại");
        }

        $user->password = bcrypt($request->new_pasword);
        $user->Auth::save();

        return redirect()->back()->with("success","Đổi mật khẩu thành công");
    }
}
