<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_registers_a_user_successfully()
    {
        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJsonStructure([
                                      'message',
                                      'token',
                                      'user' => ['id', 'name', 'email'],
                                  ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
    }

    /** @test */
    public function it_fails_registration_with_invalid_data()
    {
        $response = $this->postJson('/api/auth/register', [
            'name'     => '',
            'email'    => 'not-an-email',
            'password' => 'short',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }
}
