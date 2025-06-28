<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;


/**
 * @group Authentication
 * APIs for user registration, login, and password resets.
 */
class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @bodyParam name string required The name of the user.
     * @bodyParam email string required The email of the user.
     * @bodyParam password string required The password.
     * @bodyParam password_confirmation string required Must match password.
     *
     * @response 201 {
     *   "message": "Registered successfully",
     *   "token": "your-token-here"
     * }
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ],201);
    }

    /**
     * Login a user.
     *
     * @bodyParam email string required
     * @bodyParam password string required
     *
     * @response 200 {
     *   "message": "Login successful",
     *   "token": "your-token-here"
     * }
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ]);
    }

    /**
     * Logout the authenticated user.
     *
     * @response 200 {
     *   "message": "Logged out"
     * }
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    /**
     * Request a password reset link.
     *
     * @bodyParam email string required The user's email.
     *
     * @response 200 {
     *   "message": "Password reset link sent"
     * }
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)])
            : response()->json(['message' => __($status)], 400);
    }

    /**
     * Reset the password.
     *
     * @bodyParam token string required Reset token.
     * @bodyParam email string required Email associated with the token.
     * @bodyParam password string required New password.
     * @bodyParam password_confirmation string required Confirm password.
     *
     * @response 200 {
     *   "message": "Password has been reset"
     * }
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            fn($user, $password) => $user->update(['password' => bcrypt($password)])
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __($status)])
            : response()->json(['message' => __($status)], 400);
    }

    /**
     * Change the authenticated user's password.
     *
     * @bodyParam current_password string required
     * @bodyParam new_password string required
     * @bodyParam new_password_confirmation string required
     *
     * @response 200 {
     *   "message": "Password changed successfully"
     * }
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        if (!Hash::check($request->current_password, $request->user()->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided password is incorrect.'],
            ]);
        }

        $request->user()->update(['password' => bcrypt($request->password)]);

        return response()->json(['message' => 'Password changed successfully.']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

}
