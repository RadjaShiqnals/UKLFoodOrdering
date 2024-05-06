<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role; // Tambahkan ini

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = JWTAuth::user();
        
        return response()->json([
            'status' => true,
            'logged' => true,
            'message' => 'Login success',
            'token' => $token,
        ]);
    }

    public function register(Request $request)
    {
        // Misalnya, Anda ingin semua yang mendaftar melalui /register adalah pengguna biasa
        $role = Role::where('name', 'user')->first(); // Ambil peran pengguna biasa

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $role->id // Atur peran pengguna
        ]);

        $user->save();

        return response()->json([
            'message' => 'Successfully registered',
            'email' => $request->email,
            'password' => $request->password,
            'role_id' => $role->id
        ]);
    }
    public function registeradmin(Request $request)
    {
        // Misalnya, Anda ingin semua yang mendaftar melalui /register adalah pengguna biasa
        $role = Role::where('name', 'admin')->first(); // Ambil peran pengguna biasa

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $role->id // Atur peran pengguna
        ]);

        $user->save();

        return response()->json([
            'message' => 'Successfully registered',
            'email' => $request->email,
            'password' => $request->password,
            'role_id' => $role->id
        ]);
    }

    public function logout(Request $request)
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Successfully logged out']);
    }
}
