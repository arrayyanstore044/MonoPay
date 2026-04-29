<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Pengecekan manual untuk duplikasi email sesuai spesifikasi
        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Email already exists.',
                'data' => null
            ], 400); // Bad Request atau 409 Conflict
        }

        // Simpan user baru
        $user = User::create([
            'name' => $request->name,
            'notelp' => $request->notelp,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Enkripsi password
        ]);

        // Kirim email verifikasi
        $user->sendEmailVerificationNotification();

        // Response sukses (mengembalikan password sesuai instruksi di issue.md)
        return response()->json([
            'status' => 'OK!',
            'message' => 'User successfully registered!',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'notelp' => $user->notelp,
                    'email' => $user->email,
                ]
            ]
        ], 201);
    }
}
