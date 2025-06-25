<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use App\Http\Requests\User\UpdateUserPreferencesRequest;

class UserPreferenceController extends Controller
{
    public function show(Request $request)
    {
        $preferences = $request->user()->preference;

        return response()->json($preferences);
    }

    public function update(UpdateUserPreferencesRequest $request)
    {
        $user = $request->user();

        $user->preference()->updateOrCreate(
            ['user_id' => $user->id],
            $request->only(['preferred_sources', 'preferred_categories', 'preferred_authors'])
        );

        return response()->json(['message' => 'Preferences updated.']);
    }
}
