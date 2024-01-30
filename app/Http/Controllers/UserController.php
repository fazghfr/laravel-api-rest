<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    // get the user by token
    public function getUser(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }

    // register and logging in
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        $token = $user->createToken('tokenkey')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    //login only
    public function login(Request $request)
    {
        // input is only email and password
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $input_email = $request->email;
        $input_password = $request->password;

        try {
            $user = User::where('email', $input_email)->firstOrFail();

            if(!password_verify($input_password, $user->password)){
                return response()->json([
                    'message' => 'invalid credentials'
                ], 401);
            }

            $token = $user->createToken('token-key')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'not found'
            ], 404);
        }
    }

    //logout
    public function logout(Request $request)
    {
        // Ensure the user is authenticated
        $user = $request->user();

        if ($user) {
            // Revoke all of the user's tokens
            $user->tokens->each(function ($token, $key) {
                $token->delete();
            });

            return response()->json([
                'message' => 'Logged out successfully',
            ]);
        } else {
            return response()->json([
                'message' => 'No authenticated user',
            ], 401);
        }
    }
}
