<?php

namespace Tests\Feature\UserPreferences;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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
            ->assertJsonFragment(['message' => 'Preferences updated successfully.']);

        $this->assertTrue(
            DB::table('user_preferences')
                ->where('user_id', $this->user->id)
                ->whereJsonContains('preferred_sources', ['newsorg'])
                ->whereJsonContains('preferred_sources', ['nytimes'])
                ->whereJsonContains('preferred_categories', ['Technology'])
                ->whereJsonContains('preferred_authors', ['Jane Smith'])
                ->exists()
        );
    }

    /** @test */
    public function user_can_get_preferences()
    {
        $this->user->preference()->create(
            [
                'preferred_sources'    => ['newsorg', 'guardian'],
                'preferred_categories' => ['Politics', 'Science'],
                'preferred_authors'    => ['Alice', 'Bob'],
            ]
        );

        $response = $this->getJson('/api/preferences');

        $response->assertOk()
            ->assertJson(
                [
                    'preferred_sources'    => ['newsorg', 'guardian'],
                    'preferred_categories' => ['Politics', 'Science'],
                    'preferred_authors'    => ['Alice', 'Bob'],
                ]
            );
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
