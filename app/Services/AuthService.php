<?php

namespace App\Services;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    // This service can be used to handle authentication related logic
    public function register(object $request)
    {
        // Handle user registration logic here
        $user = User::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Send OTP to user email
        $this->otp($user, 'verification');

        return $user;

    }
    public function login(object $request)
    {
        // Handle user login logic here
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return null;
        }
        return $user;
    }

    public function otp(User $user, string $type)
    {
        // Handle OTP verification logic here
        // check for spam and throttle if necessary
        $tries = 3;
        $time = Carbon::now()->subMinutes(15);
        $otpCount = Otp::where('user_id', $user->id)
            ->where('type', $type)
            ->where('active', 1)
            ->where('created_at', '>=', $time)
            ->count();

        if ($otpCount >= $tries) {
            return response([
                'message' => __('app.otp_request_limit_exceeded'),
            ], 429);
        }

        $code = random_int(100000, 999999);
        $otp = Otp::create([
            'user_id' => $user->id,
            'type' => $type,
            'code' => $code,
            'active' => 1,
        ]);

        // send otp to user email
        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpMail($user, $code));

        return $otp;
    }

    public function verifyOtp(User $user, object $request)
    {
        // Handle OTP verification logic here
        $otp = Otp::where('user_id', $user->id)
            ->where('code', $request->otp)
            ->where('active', 1)
            ->where('type', 'verification')
            ->first();

        if (!$otp) {
            return response([
                'message' => __('app.invalid_otp'),
            ], 400);
        }

        // update
        $user->email_verified_at = Carbon::now();
        $user->update();

        // mark otp as used
        $otp->active = 0;
        $otp->updated_at = Carbon::now();
        $otp->update();

        // return
        return $user;
    }

    public function getUserByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    public function resetPassword(User $user, object $request)
    {
        // validate otp
        $otp = Otp::where('user_id', $user->id)
            ->where('code', $request->otp)
            ->where('active', 1)
            ->where('type', 'password-reset')
            ->first();
        if (!$otp) {
            return response([
                'message' => __('app.invalid_otp'),
            ], 400);
        }

        // set the new password
        $user->password = Hash::make($request->password);
        $user->updated_at = Carbon::now();
        $user->update();

         // mark otp as used
        $otp->active = 0;
        $otp->updated_at = Carbon::now();
        $otp->update();
        // return user
        return $user;
    }
}

