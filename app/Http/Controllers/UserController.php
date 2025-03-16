<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function register(RegisterRequest $request){
        $user = User::create($request->all());

        $token=$user->createToken("API TOKEN")->plainTextToken;
        return response()->json([
            'user' =>$user,
            'token' => $token,
            'success' => true,
            'message' => 'User has been register successfully'
        ], 201);
    }

    public function login(LoginRequest $request){

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        $token = $user->createToken('API TOKEN')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'=>$user,
            'success' => true,
            'message' => 'user logged in successfully'
        ],200);

    }
}
