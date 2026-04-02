<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:4',
        ]);
        // Create a new user
        $user = $this->authService->register($request);
        // create access token for the user
        $token = $user->createToken('auth_token')->plainTextToken;
        // return the access token in the response
        return response([
            'message' => __('app.registered_successfully_verified'),
            'results' => [
                'user' => new \App\Http\Resources\UserResource($user),
                'token' => $token,
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        // Validate the request data
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        // login the user
        $user = $this->authService->login($request);
        if(!$user) {
            return response([
                'message' => __('app.invalid_credentials'),
            ], 401);
        }
        // create access token for the user
        $token = $user->createToken('auth_token')->plainTextToken;
        // return the access token in the response
        return response([
            'message' => $user->email_verified_at ? __('app.logged_in_successfully') : __('app.logged_in_successfully_verified'),
            'results' => [
                'user' => new \App\Http\Resources\UserResource($user),
                'token' => $token,
            ],
        ]);
    }
    public function Otp(Request $request)
    {
        // get user
        $user = Auth::user();
        // generate otp
        $otp = $this->authService->otp($user, 'verification');

        // return success message
        return response([
            'message' => __('app.otp_sent_successfully'),
        ]);

    }

    public function verifyOtp(Request $request)
    {
        // Validate the request data
        $request->validate([
            'otp' => 'required|string',
        ]);
        // get user
        $user = Auth::user();
        // verify otp
        $user = $this->authService->verifyOtp($user, $request);

        // return success message
        return response([
            'message' => __('app.otp_verified_successfully'),
            'results' => [
                'user' => new \App\Http\Resources\UserResource($user),
            ],
        ]);
    }


    public function resetOtp(Request $request)
    {
        // Validate the request data
        $request->validate([
            'email' => 'required|string|email|exists:users,email',
        ]);
        // get user by email
        $user = $this->authService->getUserByEmail($request->email);

        // generate otp
        $otp = $this->authService->otp($user, 'password-reset');

        // return success message
        return response([
            'message' => __('app.otp_sent_successfully'),
        ]);

    }

    public function resetPassword(Request $request)
    {
        // Validate the request data
        $request->validate([
            'email' => 'required|string|email|exists:users,email',
            'otp' => 'required|string',
            'password' => 'required|string|min:4|confirmed',
            'password_confirmation' => 'required|string|min:4',
        ]);
        // get user by email
        $user = $this->authService->getUserByEmail($request->email);

        // reset password
         $user = $this->authService->resetPassword($user, $request);

        // return success message
        return response([
            'message' => __('app.password_reset_successfully'),
        ]);

    }


    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();
        // return success message
        return response([
            'message' => __('app.logged_out_successfully'),
        ]);
    }
}
