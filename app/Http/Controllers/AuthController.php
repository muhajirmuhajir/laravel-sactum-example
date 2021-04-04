<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // validate request
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed'
        ]);
        // hash password
        $fields['password'] = bcrypt($fields['password']);

        // create user
        $user = User::create($fields);
        $token = $user->createToken('myApptoken')->plainTextToken;

        return response([
            'user' => $user,
            'token' =>  $token
        ], 201);
    }
}
