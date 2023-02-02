<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class UserAuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid password and email combination',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();
        return response()->json([
                'status' => 'success',
                'user' => $user,
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);

    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);


        $user = User::create([
            'user_name' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        
        $token = Auth::login($user);

        return response()->json([
            'status' => 'success',
            'message' => 'Account created successfully',
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }


public static function getUser(Request $request)
{
    if(!$request->bearerToken())
    {
        return null;
    }

    $user = Auth::authenticate($request->bearerToken());
    return $user;
}

/*

There would be no logout functionality because you do not log out of APIs using JWT for authentication
And tokens do not change for an account
Hence, if you invalidate the token, user cannot make any api calls

Possible crude bypass for this problem
On logout, invalidate token
ON login, validate token

Better solution:
Handle the logout functionality on the client side


Same thing for refresh functionality

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }



    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorization' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

*/


}
