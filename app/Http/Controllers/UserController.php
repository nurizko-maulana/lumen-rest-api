<?php

namespace App\Http\Controllers;

use App\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request){
        $this->validate($request, [
            'email' => 'required|unique:user|email',
            'password' => 'required|min:6'
        ]);

        $email = $request->input('email');
        $password = $request->input('password');
        $hashPassword = Hash::make($password);

        $user = Users::create([
            'email' => $email,
            'password' => $hashPassword
        ]);

        return response()->json(['message' => "Success"], 201);
    }

    public function login(Request $request){
        $this->validate($request , [
            'email' => 'required|unique:user|email',
            'password' => 'required|min:6'
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        $user = Users::where('email', $email)->first();
        if(!$user){
            return response()->json(['message' => 'Login failed!'], 401);
        }

        $isValidatePassword = Hash::check($password, $user->password);
        if(!$isValidatePassword){
            return response()->json(['message' => 'Login failed!'], 401);
        }

        $generateToken = bin2hex(random_bytes(40));

        $user->update([
            'token' => $generateToken
        ]);

        return response()->json($user);
    }
}
