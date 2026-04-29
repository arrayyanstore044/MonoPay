<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_login_successfully()
    {
        $password = 'password123';
        $user = User::factory()->create([
            'email' => 'jhon@example.com',
            'password' => Hash::make($password),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'jhon@example.com',
            'password' => $password,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'OK!',
                     'message' => 'User successfully registered!', // Sesuai spesifikasi yang diminta
                     'data' => [
                         'user' => [
                             'id' => $user->id,
                             'name' => $user->name,
                         ]
                     ]
                 ]);

        $this->assertDatabaseHas('user_session', [
            'user_id' => $user->id,
        ]);

        $this->assertNotNull($response->json('data.user.token'));
    }

    public function test_cannot_login_with_wrong_credentials()
    {
        User::factory()->create([
            'email' => 'jhon@example.com',
            'password' => Hash::make('correct_password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'jhon@example.com',
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'status' => 'ERROR',
                     'message' => 'Wrong email or password.',
                     'data' => null
                 ]);
    }
}
