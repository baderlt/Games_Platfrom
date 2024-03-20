<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\User_login;
use App\Http\Requests\User_Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create User
     * @param Request $request
     * @return User 
     */



    ///////// register for user with name and password 
    public function createUser(User_Request $request)
    {
        try {
            // Create a new user 
            $user = User::create([
                'name' => $request->name,
                'password' => Hash::make($request->password)
            ]);
            // Generate an API token for the new user and   and Return a JSON response with success status, token, and a 201 status code
            return response()->json([
                'status' => true,
                'token' => $user->createToken("API TOKEN", ['*'], now()->addHour())->plainTextToken
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Login The User
     * @param Request $request
     * @return User
     */

    /////login for user 
    public function loginUser(User_login $request)
    {
        try {
            //// get the user 
            $user = User::where('name', $request->name)->first();
            ////// check if user is blocked
            if ($user->blocked == 1) {
                return response()->json(['status' => 'blocked', 'message' => 'User Blocked ', 'reason' => $user->reason], 403);
            }
            // Attempt to authenticate the user using the provided name and password
            if (Auth::attempt($request->only(['name', 'password']))) {
                // Update the last connection time for the user
                $user->lastConextion = now();
                $user->save();
                // Generate an API token for the authenticated user and return the response json 

                return response()->json([
                    'status' => true,

                    'token' => $user->createToken("API TOKEN", ['*'], now()->addHour())->plainTextToken
                ], 200);
            }
            // Return a JSON response with an invalid status if the authentication fails
            return response()->json([
                'status' => "invalide",
                'message' => "Nom d'utilisateur ou mot de passe incorrect",
            ], 401);
        } catch (\Throwable $th) {
            // If an error occurs during the process, return an error response with the error message
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function logout()
    {
        try {
            // Check if the user is authenticated
            if (Auth::check()) {
                // Delete all access tokens associated with the authenticated user
                Auth::user()->tokens()->delete();
                // Return a JSON response with success status and a 200 status code
                return response()->json(['status' => "succès"], 200);
            }

            // If not authenticated return an error response
            return response()->json(['status' => false, 'message' => 'User not authenticated'], 401);
        } catch (\Throwable $th) {
            // If an error occurs during the process, return an error response with the error message
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
