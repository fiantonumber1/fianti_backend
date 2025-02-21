<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    // REGISTER
    public function register(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        // Membuat user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Membuat token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Mengirimkan response dengan token
        return response()->json([
            'message' => 'Registrasi berhasil, silakan login!',
            'user' => $user,
            'token' => $token,  // Menambahkan token di response
        ], 201);
    }

    // LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Login gagal, email atau password salah!'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil!',
            'token' => $token,
            'id' => (int) $user->id,       // Pastikan ID dikembalikan dalam bentuk integer
            'name' => $user->name,        // Mengembalikan nama pengguna
        ], 200);
    }

    public function updateVerification(Request $request)
    {
        // Validasi input
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'is_verified' => 'required|boolean',
        ]);

        // Ambil user berdasarkan ID
        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Update status is_verified
        $user->is_verified = $request->is_verified;
        $user->save();

        return response()->json([
            'message' => 'User verification updated successfully',
            'user' => [
                'id' => $user->id,
                'is_verified' => $user->is_verified
            ]
        ], 200);
    }


    // LOGOUT
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout berhasil!'
        ], 200);
    }

}
