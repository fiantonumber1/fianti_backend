<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class VerificationController extends Controller
{
    public function updateVerification(Request $request)
    {
        // Validasi input
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'isverified' => 'required|boolean',
        ]);

        // Ambil user berdasarkan ID
        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Update status is_verified
        $user->isverified = $request->isverified;
        $user->save();

        return response()->json([
            'message' => 'User verification updated successfully',
            'user' => [
                'id' => $user->id,
                'isverified' => $user->is_verified
            ]
        ], 200);
    }
}
