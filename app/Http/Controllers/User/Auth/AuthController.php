<?php

namespace App\Http\Controllers\User\Auth;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Twilio\Rest\Client;

class AuthController extends Controller{

    public function register(Request $request){
        if(User::where('mobile_no',$request->mobile_no)->first()){
            return response([
                'message' =>  'Số điện thoại đã được sử dụng',
            ], 500);
        }
        $user=User::create($request->all());
        return response([
            'message'=>'Đăng ký thành công',
            'user'=>$user,
        ], 200);
    }

    public function login(Request $request){
        $user=User::where('mobile_no', $request->mobile_no)->first();
        if($user) {
            $sendOtp = $this->sendSmsNotification($user);
            return response([
                'message'=>'Đăng nhập thành công',
                'user'=>$user,
            ], 200);
            } else return response([
                'message'=>'Số điện thoại chưa được đăng ký',
            ],500);
    }

    public function logout(){
        Auth::logout();
        return response()->json(['message' => 'Đã đăng xuất']);
    }

    public function sendSmsNotification($user)
    {
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_TOKEN");
        $twilio_number = getenv("TWILIO_FROM");
        $client = new Client($account_sid, $auth_token);
        
        $receiverNumber="0394896395";
        $otp = $this->generate($user);
        $message = 'Your OTP to login is: '.$otp;

        $result=$client->messages->create($receiverNumber, [
            'from' => $twilio_number, 
            'body' => $message]);
        if ($result) {
            return "The message was sent successfully\n";
        } else {
            return "The message failed";
        }
    }

    public function generate($user)
    {
        # Generate An OTP
        $verificationCode = $this->generateOtp($user);
        
        # Return With OTP 
        return $verificationCode->otp; 
    }

    public function generateOtp($user)
    {
        # User Does not Have Any Existing OTP
        $verificationCode = VerificationCode::where('user_id', $user->id)->latest()->first();

        $now = Carbon::now();

        if($verificationCode && $now->isBefore($verificationCode->expire_at)){
            return $verificationCode;
        }

        // Create a New OTP
        return VerificationCode::create([
            'user_id' => $user->id,
            'otp' => rand(000000, 999999),
            'expire_at' => Carbon::now()->addMinutes(3)
        ]);
    }

    // public function changePassword(Request $request){
    //     $this->validate($request, [
    //         'old_password'=>'required|min:8|max:100',
    //         'new_password'=>'required|min:8|max:100',
    //         'confirm_password'=>'required|same:new_password'
    //     ]);
    //     $password = User::find(auth()->user()->id);
    //     $checked=Hash::check($request->old_password,$password->password);
    //     if (!$checked) {
    //         // The passwords matches
    //         return response([
    //             "message"=>"Mật khẩu không đúng. Vui lòng nhập lại"
    //         ]);
    //     }

    //     if(strcmp($request->get('old_password'), $request->get('new_password')) == 0){
    //         //Current password and new password are same
    //         return response([
    //             "message"=>"Mật khẩu mới không được trùng với mật khẩu cũ. Vui lòng nhập lại"
    //         ]);
    //     }
    //     User::whereId(auth()->user()->id)->update([
    //         'password' => Hash::make(($request->new_pasword))
    //     ]);

    //     return response([
    //         'message'=>'Đổi mật khẩu thành công',
    //         'user'=>auth()->user(),
    //     ], 200);
    // }
}