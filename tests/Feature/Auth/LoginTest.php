<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_logs_in_a_user_successfully()
    {
        $user = User::factory()->create(
            [
                'password' => bcrypt('password123'),
            ]
        );

        $response = $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonStructure(
                [
                    'token',
                    'user' => ['id', 'name', 'email'],
                ]
            );
    }

    /** @test */
    public function it_fails_login_with_invalid_credentials()
    {
        $user = User::factory()->create(
            [
                'password' => bcrypt('password123'),
            ]
        );

        $response = $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)->assertJson(
            [
                'message' => 'Invalid credentials',
            ]
        );
    }
}
