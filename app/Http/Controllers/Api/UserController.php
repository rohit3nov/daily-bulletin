<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Http\Resources\ArticleResource;

/**
 * @group User Profile
 * Manage profile and fetch current user.
 */
class UserController extends Controller
{
    /**
     * Get the current authenticated user.
     *
     * @authenticated
     *
     * @response 200 {
     *   "id": 1,
     *   "name": "John Doe",
     *   "email": "john@example.com"
     * }
     */
    public function index(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Update the authenticated user's profile.
     *
     * @authenticated
     *
     * @bodyParam name string optional
     * @bodyParam email string optional
     *
     * @response 200 {
     *   "message": "Profile updated successfully"
     * }
     */
    public function update(UpdateProfileRequest $request)
    {
        $request->user()->update($request->validated());

        return response()->json(['message' => 'Profile updated successfully.']);
    }

    /**
     * Get personalized feed.
     *
     * @authenticated
     *
     * @queryParam page integer Page number.
     *
     * @response 200 {
     *   "data": [...],
     *   "meta": {...}
     * }
     */
    public function feed(Request $request)
    {
        $preferences = $request->user()->preference;

        $query = Article::with('category');

        if ($preferences) {
            if ($preferences->preferred_sources) {
                $query->whereIn('source', $preferences->preferred_sources);
            }

            if ($preferences->preferred_categories) {
                $query->whereHas('category', function ($q) use ($preferences) {
                    $q->whereIn('name', $preferences->preferred_categories);
                });
            }

            if ($preferences->preferred_authors) {
                $query->whereIn('author', $preferences->preferred_authors);
            }
        }

        return ArticleResource::collection($query->paginate(10));
    }

}
