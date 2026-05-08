<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\ApiBaseController;
use App\Mail\EmailVerification;
use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\PersonalAccessToken;

class RegisterController extends ApiBaseController
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|in:buyer,seller',
        ]);

        $user = DB::transaction(function () use ($validated): User {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
            ]);

            $user->assignPlatformRole($validated['role'] ?? 'buyer');
            $user->wallet()->create(['balance' => 0]);

            return $user;
        });

        $verificationToken = $user->createToken(
            'email-verification',
            ['email:verify'],
            now()->addDay()
        )->plainTextToken;
        Mail::to($user->email)->queue(new EmailVerification($user, $verificationToken));
        Mail::to($user->email)->queue(new WelcomeEmail($user));

        $token = $user->createToken('auth-token')->plainTextToken;

        return $this->successResponse([
            'user' => $user->fresh()->load('wallet'),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Registrasi berhasil. Silakan verifikasi email Anda.', 201);
    }

    public function verifyEmail(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => 'required|string',
        ]);

        $accessToken = PersonalAccessToken::findToken($validated['token']);

        if (
            ! $accessToken ||
            $accessToken->name !== 'email-verification' ||
            ! ($accessToken->tokenable instanceof User) ||
            ($accessToken->expires_at && $accessToken->expires_at->isPast())
        ) {
            return $this->errorResponse('Token verifikasi email tidak valid atau sudah kadaluarsa.', 400);
        }

        $user = $accessToken->tokenable;

        if ($user->email_verified_at) {
            $accessToken->delete();

            return $this->errorResponse('Email sudah diverifikasi.', 400);
        }

        $user->update(['email_verified_at' => now()]);
        $accessToken->delete();

        return $this->successResponse(null, 'Email berhasil diverifikasi.');
    }

    public function resendVerification(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->email_verified_at) {
            return $this->errorResponse('Email sudah diverifikasi.', 400);
        }

        $verificationToken = $user->createToken(
            'email-verification',
            ['email:verify'],
            now()->addDay()
        )->plainTextToken;
        Mail::to($user->email)->queue(new EmailVerification($user, $verificationToken));

        return $this->successResponse(null, 'Email verifikasi telah dikirim ulang.');
    }
}
