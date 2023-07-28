<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validation rules for user registration
        $request->validate([
            "name" => "required|string|max:100",
            "email" => "required|email|unique:users,email",
            "password" => "required|min:8|confirmed",
        ]);

        // Create a new user in the database
        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
        ]);

        // Return a JSON response with success message upon successful registration
        return response()->json([
            "success" => true,
            "message" => $user->name . " is registered successfully.",
        ]);
    }

    public function login(Request $request)
    {
        // Validation rules for user login
        $request->validate([
            "email" => "required|email",
            "password" => "required|min:8",
        ]);

        // Attempt to authenticate the user with provided credentials
        if (!Auth::attempt($request->only('email', 'password'))) {
            // Return a JSON response with error message if authentication fails
            return  response()->json([
                "success" => false,
                "message" => "Email or Password wrong.",
            ], 401);
        }

        // If authentication is successful, generate a new API token for the user
        return  response()->json([
            "success" => true,
            "message" => "Login Successful.",
            "data" => Auth::user(),
            "token" => Auth::user()->createToken($request->has("device") ? $request->device : "unknown device")->plainTextToken,
        ]);
    }

    public function logout()
    {
        // Revoke the current access token of the authenticated user
        Auth::user()->currentAccessToken()->delete();

        // Return a JSON response with success message upon successful logout
        return  response()->json([
            "success" => true,
            "message" => "Logout Successful.",
        ]);
    }

    public function logoutAll()
    {
        // Revoke all access tokens of the authenticated user (log out from all devices)
        foreach (Auth::user()->tokens as $token) {
            $token->delete();
        }

        // Return a JSON response with success message upon successful logout from all devices
        return response()->json([
            "success" => true,
            "message" => "Log out all devices Successful",
        ]);
    }

    public function devices()
    {
        // Get all access tokens associated with the authenticated user (active login devices)
        return Auth::user()->tokens;
    }
}
