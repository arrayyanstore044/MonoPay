<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Register
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

        // Generate link verifikasi untuk dikembalikan di response
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

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
                ],
                'verification_url' => $verificationUrl
            ]
        ], 201);
    }

    // Login
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        // Validasi Email & Password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Wrong email or password.',
                'data' => null
            ], 401);
        }

        // Generate UUID token & Expired at
        $token = Str::uuid()->toString();
        $expiredAt = now()->addHours(24);

        // Simpan ke tabel user_session
        UserSession::create([
            'token' => $token,
            'user_id' => $user->id,
            'expired_at' => $expiredAt,
        ]);

        // Response sukses (menggunakan message sesuai spesifikasi issue)
        return response()->json([
            'status' => 'OK!',
            'message' => 'User successfully registered!',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'token' => $token
                ]
            ]
        ], 200);
    }
}
