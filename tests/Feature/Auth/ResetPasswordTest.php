<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_resets_password_successfully()
    {
        Notification::fake();

        $user = User::factory()->create();

        $token = Password::createToken($user);

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertOk()
            ->assertJson(['message' => 'Password has been reset successfully.']);

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    /** @test */
    public function it_fails_with_invalid_token()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => $user->email,
            'token' => 'invalid-token',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
