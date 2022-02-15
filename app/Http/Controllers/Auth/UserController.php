<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    //GET: /user/changepass
    public function changepassview(){
        if(auth()->user()->can("Reset Password Everyone"))
            $users = User::all();
        else
            $users = collect([auth()->user()]);
        return view("user.changepass", compact("users"));
    }

    //PATCH: /user/changepass
    public function changepass(Request $request){
        if(!auth()->user()->can("Reset Password Everyone")){
            $data = $this->validate($request, [
                "old_password" => "required|current_password",
                "password" => "required|confirmed"
            ]);
            auth()->user()->update([
                "password" => Hash::make($data['password'])
            ]);
        }
        else{
            $data = $this->validate($request, [
                "user_id" => "required|exists:App\Models\User,id",
                "old_password" => "required|current_password",
                "password" => "required|confirmed"
            ]);
            User::find($data["user_id"])->update([
                "password" => Hash::make($data['password'])
            ]);
        }
        return redirect()->route("home")->with("message", ["type" => "success", "content" => "<b>Password</b> has been changed."]);
    }
}
