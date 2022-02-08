<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //GET: /user/changepass
    public function changepassview(){
        return view("user.changepass");
    }

    //PATCH: /user/changepass
    public function changepass(Request $request){
        $data = $this->validate($request, [
            "old_password" => "required|current_password",
            "password" => "required|confirmed"
        ]);
        auth()->user()->update([
            "password" => Hash::make($data['password'])
        ]);
        return redirect()->route("home")->with("message", ["type" => "success", "content" => "<b>Password</b> has been changed."]);
    }
}
