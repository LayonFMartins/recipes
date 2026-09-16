<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function register(RegisterRequest $request){
        $user = User::create($request->validated());
        $token = $user->createToken("api")->plainTextToken;

        return response()->json([
            "user"  => $user,
            "token" => $token,
        ], 201);
    }

    public function login(LoginRequest $request){

        $credentials = $request->validated();
        
        if(! Auth::attempt($credentials))
            return response()->json(["message" => "Credenciais Inválidas."], 401);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $token = $user->createToken("api")->plainTextToken; 
   
        return response()->json([
            "user"   => $user, 
            "token"  => $token,
        ]);
    }
}
