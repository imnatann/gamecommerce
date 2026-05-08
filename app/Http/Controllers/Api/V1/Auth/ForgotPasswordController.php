<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\ApiBaseController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends ApiBaseController
{
    public function sendResetLink(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $throttleKey = 'password-reset:' . $validated['email'];

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return $this->errorResponse("Terlalu banyak permintaan. Coba lagi dalam {$seconds} detik.", 429);
        }

        RateLimiter::hit($throttleKey, 300);

        $status = Password::sendResetLink($validated);

        if ($status === Password::RESET_LINK_SENT) {
            return $this->successResponse(null, 'Link reset password telah dikirim ke email Anda.');
        }

        return $this->errorResponse('Gagal mengirim link reset password.', 400);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset($validated, function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->setRememberToken(str()->random(60));

            $user->save();
            $user->tokens()->delete();
        });

        if ($status === Password::PASSWORD_RESET) {
            return $this->successResponse(null, 'Password berhasil direset.');
        }

        return $this->errorResponse('Token reset password tidak valid atau sudah kadaluarsa.', 400);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $request->user()->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Password saat ini salah.'],
            ]);
        }

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $request->user()->tokens()->delete();

        return $this->successResponse(null, 'Password berhasil diubah. Silakan login kembali.');
    }
}