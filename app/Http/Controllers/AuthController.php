<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:6'
        ]);

        // check email
        $user = User::where('email', $fields['email'])->first();

        // check passwords

        if (!$user || !Auth::attempt($fields)) {
            return response([
                'message' => 'invalid email or password',
            ], 401);
        };

        $token = $user->createToken('myApptoken')->plainTextToken;

        return response([
            'user' => $user,
            'token' =>  $token
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response([
            'message' => 'logged out',
        ], 200);
    }
}
