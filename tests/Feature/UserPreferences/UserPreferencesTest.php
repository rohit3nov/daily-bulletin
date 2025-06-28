<?php

namespace Tests\Feature\UserPreferences;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPreferencesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function user_can_set_preferences()
    {
        $payload = [
            'preferred_sources'    => ['newsorg', 'nytimes'],
            'preferred_categories' => ['Technology', 'Health'],
            'preferred_authors'    => ['John Doe', 'Jane Smith']
        ];

        $response = $this->putJson('/api/preferences', $payload);

        $response->assertOk()
            ->assertJsonFragment(['message' => 'Preferences updated successfully']);

        $this->assertDatabaseHas('users', [
            'id'                   => $this->user->id,
            'preferred_sources'    => json_encode($payload['preferred_sources']),
            'preferred_categories' => json_encode($payload['preferred_categories']),
            'preferred_authors'    => json_encode($payload['preferred_authors']),
        ]);
    }

    /** @test */
    public function user_can_get_preferences()
    {
        $this->user->update([
                                'preferred_sources'    => ['newsorg', 'guardian'],
                                'preferred_categories' => ['Politics', 'Science'],
                                'preferred_authors'    => ['Alice', 'Bob'],
                            ]);

        $response = $this->getJson('/api/preferences');

        $response->assertOk()
            ->assertJson([
                             'preferred_sources'    => ['newsorg', 'guardian'],
                             'preferred_categories' => ['Politics', 'Science'],
                             'preferred_authors'    => ['Alice', 'Bob'],
                         ]);
    }

    /** @test */
    public function validation_errors_are_returned_for_invalid_preferences()
    {
        $response = $this->putJson('/api/preferences', [
            'preferred_sources' => 'not-an-array',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['preferred_sources']);
    }

    /** @test */
    public function unauthenticated_users_cannot_access_preferences()
    {
        auth()->logout();

        $this->getJson('/api/preferences')->assertUnauthorized();
        $this->putJson('/api/preferences', [])->assertUnauthorized();
    }
}
