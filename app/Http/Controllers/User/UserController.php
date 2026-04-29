<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getCurrentUser(Request $request)
    {
        $user = Auth::user();

        return response()->json([
            'status' => 'OK!',
            'message' => 'Get Current User Success!',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at,
                ]
            ]
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $token = $request->bearerToken();

        // Hapus session berdasarkan token
        UserSession::where('token', $token)->delete();

        return response()->json([
            'status' => 'OK!',
            'message' => 'User Log Out Successfully!',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at,
                ]
            ]
        ], 200);
    }
}
