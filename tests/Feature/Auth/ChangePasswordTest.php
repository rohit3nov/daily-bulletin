<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_changes_password_for_authenticated_user()
    {
        $user = User::factory()->create([
                                            'password' => bcrypt('oldpassword'),
                                        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/auth/change-password', [
            'current_password'      => 'oldpassword',
            'password'              => 'newsecurepassword',
            'password_confirmation' => 'newsecurepassword',
        ]);

        $response->assertOk()
            ->assertJson([
                             'message' => 'Password changed successfully.',
                         ]);
    }

    /** @test */
    public function it_fails_to_change_password_with_wrong_current_password()
    {
        $user = User::factory()->create([
                                            'password' => bcrypt('correct-password'),
                                        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/auth/change-password', [
            'current_password'      => 'wrong-password',
            'password'              => 'newsecurepassword',
            'password_confirmation' => 'newsecurepassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    }
}
