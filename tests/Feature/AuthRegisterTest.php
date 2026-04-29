<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthRegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_register_user()
    {
        $payload = [
            "name" => "Jhon",
            "notelp" => "08123456789",
            "email" => "jhon@example.com",
            "password" => "password"
        ];

        $response = $this->postJson('/api/auth/register', $payload);

        $response->assertStatus(201)
                 ->assertJson([
                     'status' => 'OK!',
                     'message' => 'User successfully registered!',
                     'data' => [
                         'user' => [
                             'name' => 'Jhon',
                             'notelp' => '08123456789',
                             'email' => 'jhon@example.com'
                         ]
                     ]
                 ]);

        $this->assertDatabaseHas('users', ['email' => 'jhon@example.com']);
    }

    public function test_cannot_register_with_existing_email()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $payload = [
            "name" => "Jane",
            "notelp" => "08123456780",
            "email" => "existing@example.com",
            "password" => "password"
        ];

        $response = $this->postJson('/api/auth/register', $payload);

        $response->assertStatus(400)
                 ->assertJson([
                     'status' => 'ERROR',
                     'message' => 'Email already exists.',
                     'data' => null
                 ]);
    }
}
