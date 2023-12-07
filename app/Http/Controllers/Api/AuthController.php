<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // firstly we validate the user's inputs
        $request->validate([
           "email" => "required | email",
           "password" => "required"
        ]);

        // then we fetch the user from the DB
        $user = User::where("email", $request->email)->first();
        if(!$user) throw ValidationException::withMessages(["User not found with provided credentials"]);
        // compare hash value and the plain text password
        if(!Hash::check($request->password, $user->password)) throw ValidationException::withMessages(["User not found with provided credentials"]);
        // if everything is valid we create a token
        $token = $user->createToken("api-token")->plainTextToken;
        // then we return the token
        return response()->json([
            "token" => $token
        ]);
    }

    public function logout(Request $request)
    {

    }
}
