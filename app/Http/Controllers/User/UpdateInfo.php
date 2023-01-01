<?
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class UpdateInfo extends Controller{
    public function update(Request $request){
        $user=User::where("id",$request->id)->update($request->all());
        return response([
            'user'=>$user
        ]);
    }
}