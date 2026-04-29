<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserSession;
use App\Http\Resources\User\UserResource;
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
                'user' => new UserResource($user)
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
                'user' => new UserResource($user)
            ]
        ], 200);
    }
}
