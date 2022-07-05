<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserInterest;


class LoginController extends Controller
{
    public function register(Request $request)
    {
       
      // print_r($request->profile_pic);die;
        if(!empty($request->file('profilepic'))){
            $destinationPath = base_path('images');
            $profilepic = time().'.'.$request->file('profilepic')->getClientOriginalExtension();
            $request->file('profilepic')->move($destinationPath, $profilepic); 
        }else{
            $profilepic = '';
        }
       // print_r($profilepic);die;
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'country' => $request->country,
            'profile_pic' => $profilepic,
          ]);
  
          return response()->json('User successfully registered');
    }
}
