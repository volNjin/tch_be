<?php

namespace App\Http\Controllers\User\Auth;

use Carbon\Carbon;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\AddressNote;
use App\Models\VerificationCode;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Twilio\Rest\Client;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        if (User::where('mobile_no', $request->mobile_no)->first()) {
            return response([
                'message' =>  'Số điện thoại đã được sử dụng',
            ], 500);
        }

        $user = User::create($request->all());
        AddressNote::create([
            'user_id' => $user->user_id,
            'user_name' => $user->user_name,
            'address' => $user->address,
            'mobile_no' => $user->mobile_no,
        ]);
        return response([
            'message' => 'Đăng ký thành công',
            'user' => $user,
        ], 200);
    }

    public function login(Request $request)
    {
        $user = User::where('mobile_no', $request->mobile_no)->first();
        if (!$user) {
            $user = User::create([
                'last_name' => 'Guest',
                'mobile_no' => $request->mobile_no,
                'birth' => DB::raw('CURRENT_TIMESTAMP'),
            ]);
        }

        $sendOtp = $this->sendSmsNotification($user);

        if ($sendOtp) return response([
            'error' => false,
        ], 200);
        else return response([
            'error' => true,
        ], 500);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Đã đăng xuất']);
    }

    public function checkOtp(Request $request)
    {
        $user = User::where('mobile_no', $request->mobile_no)->first();
        $verificationCode = VerificationCode::where('user_id', $user->id)->latest()->first();

        $now = Carbon::now();

        if (strcmp($verificationCode, $request->otp) && $now->isBefore($verificationCode->expire_at)) {
            return response([
                'userInfo' => $user,
            ]);
        } else return response([
            'userInfo' => NULL,
        ]);
    }

    public function sendSmsNotification($user)
    {
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_TOKEN");
        $twilio_number = getenv("TWILIO_FROM");
        $client = new Client($account_sid, $auth_token);
        $receiverNumber = $user->mobile_no;
        $otp = $this->generate($user);
        $message = 'Your OTP to login is: ' . $otp;

        $result = $client->messages->create($receiverNumber, [
            'from' => $twilio_number,
            'body' => $message
        ]);
        return $result;
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

        if ($verificationCode && $now->isBefore($verificationCode->expire_at)) {
            return $verificationCode;
        }

        // Create a New OTP
        return VerificationCode::create([
            'user_id' => $user->id,
            'otp' => rand(000000, 999999),
            'expire_at' => Carbon::now()->addMinutes(3)
        ]);
    }

    public function addMobileNumToTwilio(Request $request)
    {
        $sid = getenv("TWILIO_SID");
        $token = getenv("TWILIO_TOKEN");
        $twilio = new Client($sid, $token);

        $validation_request = $twilio->validationRequests
            ->create(
                $request->mobile_no, // phoneNumber
                ["friendlyName" => "My Home Phone Number"]
            );
        return $validation_request;
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