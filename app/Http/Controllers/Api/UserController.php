<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function updateProfile(UpdateProfileRequest $request)
    {
        $request->user()->update($request->validated());

        return response()->json(['message' => 'Profile updated successfully.']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
