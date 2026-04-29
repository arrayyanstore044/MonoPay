<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserCurrentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_current_user_with_valid_token()
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
                         ->getJson('/api/users/current');

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'OK!',
                     'message' => 'Get Current User Success!',
                     'data' => [
                         'user' => [
                             'id' => $user->id,
                             'name' => 'Jhon',
                             'email' => 'jhon@example.com',
                         ]
                     ]
                 ]);
    }

    public function test_cannot_get_current_user_with_invalid_token()
    {
        $response = $this->withHeader('Authorization', 'Bearer invalid-token')
                         ->getJson('/api/users/current');

        $response->assertStatus(401)
                 ->assertJson([
                     'status' => 'ERROR',
                     'message' => 'UnAuthorized.',
                     'data' => null
                 ]);
    }

    public function test_cannot_get_current_user_without_token()
    {
        $response = $this->getJson('/api/users/current');

        $response->assertStatus(401)
                 ->assertJson([
                     'status' => 'ERROR',
                     'message' => 'UnAuthorized.',
                     'data' => null
                 ]);
    }
}
