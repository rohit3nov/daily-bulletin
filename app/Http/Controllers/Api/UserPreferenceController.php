<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use App\Http\Requests\User\UpdateUserPreferencesRequest;

/**
 * @group User Preferences
 * Set and fetch user preferences and personalized feed.
 */
class UserPreferenceController extends Controller
{
    /**
     * Get user preferences.
     *
     * @authenticated
     *
     * @response 200 {
     *   "sources": ["newsorg", "guardian"],
     *   "categories": ["Technology", "Science"]
     * }
     */
    public function show(Request $request)
    {
        $preferences = $request->user()->preference;

        return response()->json($preferences);
    }

    /**
     * Update user preferences.
     *
     * @authenticated
     *
     * @bodyParam sources array required List of source keys.
     * @bodyParam categories array required List of category names.
     *
     * @response 200 {
     *   "message": "Preferences updated"
     * }
     */
    public function update(UpdateUserPreferencesRequest $request)
    {
        $user = $request->user();

        $user->preference()->updateOrCreate(
            ['user_id' => $user->id],
            $request->only(['preferred_sources', 'preferred_categories', 'preferred_authors'])
        );

        return response()->json(['message' => 'Preferences updated successfully.']);
    }
}
