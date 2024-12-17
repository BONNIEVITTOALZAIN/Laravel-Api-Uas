<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role ?? 'user',
        ]);

        return response()->json([
            'token' => $user->createToken('MDPApp')->plainTextToken,
            'user' => $user,
        ], Response::HTTP_CREATED);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('MDPApp')->plainTextToken;

            // $route = '';
            // if ($user->role == 'admin') { // blom pasti route nyo nak kemano
            //     $route = route('admin.dashboard'); 
            // } elseif ($user->role == 'user') {
            //     $route = route('user.dashboard'); 
            // }

            return response()->json([
                'message' => 'Berhasil Login',
                'token' => $token,
                'name' => $user->name,
                'role' => $user->role,
                // 'redirect' => $route, 
            ], 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Berhasil Logout',
        ], 200);
    }
}
