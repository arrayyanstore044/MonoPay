<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
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
}
