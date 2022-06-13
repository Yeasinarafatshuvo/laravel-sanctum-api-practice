<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);
    
        if($validator->fails())
        {
            return response()->json([
                'status' => 400,
                'message' => 'Bad Request'
            ]);
        }
      
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json([
            'status' => 200,
            'message' => 'User Created Successfully'
        ]);


    }

    public function login(Request $request)
    {
        
        $validator = Validator::make($request->all(),[
            'email' => 'required',
            'password' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status' => 400,
                'message' => 'Bad Request'
            ]);
        }
        
        $credentials = $request->only('email', 'password');

        // if (!Auth::attempt($credentials))
        // {
        //     return response()->json([
        //         'status' => 500,
        //         'message' => 'Unauthorized'
        //     ]);
        // }
        
        $user  = User::where('email',$request->email)->first();
        if(Hash::check(request('password'), $user->password))
        {

            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return response()->json([
                'status' => 200,
                'token' => $tokenResult
            ]);
        }

    }

    public function logOut(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Token Deleted Successfully'
        ]);
    }

    public function user_data()
    {
        $user_data = User::all();
        return $user_data;
    }


}
