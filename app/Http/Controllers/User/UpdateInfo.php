<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UpdateInfo extends Controller{
    public function update(Request $request){
        $user=User::where("id",$request->id)->first();
        $user->update($request->all());
        return response([
            'user'=>$user
        ]);
    }
}