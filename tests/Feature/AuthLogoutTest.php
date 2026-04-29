<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthLogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_logout_successfully()
    {
        $user = User::factory()->create([
            'name' => 'Jhon',
            'email' => 'jhon@example.com',
        ]);

        $token = Str::uuid()->toString();
        UserSession::create([
            'token' => $token,
            'user_id' => $user->id,
            'expired_at' => now()->addHours(24),
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->deleteJson('/api/users/logout');

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'OK!',
                     'message' => 'User Log Out Successfully!',
                     'data' => [
                         'user' => [
                             'id' => $user->id,
                             'name' => 'Jhon',
                             'email' => 'jhon@example.com',
                         ]
                     ]
                 ]);

        // Pastikan token sudah dihapus dari database
        $this->assertDatabaseMissing('user_session', [
            'token' => $token,
        ]);
    }

    public function test_token_is_invalidated_after_logout()
    {
        $user = User::factory()->create();
        $token = Str::uuid()->toString();
        UserSession::create([
            'token' => $token,
            'user_id' => $user->id,
            'expired_at' => now()->addHours(24),
        ]);

        // Logout
        $this->withHeader('Authorization', 'Bearer ' . $token)
             ->deleteJson('/api/users/logout');

        // Coba akses current user lagi dengan token yang sama
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/users/current');

        $response->assertStatus(401)
                 ->assertJson([
                     'status' => 'ERROR',
                     'message' => 'UnAuthorized.',
                     'data' => null
                 ]);
    }
}
