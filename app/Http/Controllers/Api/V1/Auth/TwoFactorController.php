<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\ApiBaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends ApiBaseController
{
    public function enable(Request $request): JsonResponse
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        if (!\Hash::check($request->password, $request->user()->password)) {
            throw ValidationException::withMessages([
                'password' => ['Password salah.'],
            ]);
        }

        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        $request->user()->update([
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode(
                collect(range(1, 8))->map(fn () => str()->random(10))->toArray()
            )),
        ]);

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $request->user()->email,
            $secret
        );

        return $this->successResponse([
            'secret' => $secret,
            'qr_code_url' => $qrCodeUrl,
            'recovery_codes' => json_decode(decrypt($request->user()->two_factor_recovery_codes)),
        ], '2FA berhasil diaktifkan. Verifikasi dengan kode dari aplikasi authenticator Anda.');
    }

    public function verify(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|size:6',
            'user_id' => 'required|exists:users,id',
        ]);

        $user = \App\Models\User::findOrFail($validated['user_id']);

        if (!$user->two_factor_secret) {
            return $this->errorResponse('2FA tidak diaktifkan untuk user ini.', 400);
        }

        $google2fa = new Google2FA();
        $secret = decrypt($user->two_factor_secret);
        $valid = $google2fa->verifyKey($secret, $validated['code']);

        if (!$valid) {
            throw ValidationException::withMessages([
                'code' => ['Kode 2FA tidak valid.'],
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return $this->successResponse([
            'user' => $user->load('wallet'),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Verifikasi 2FA berhasil.');
    }

    public function disable(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = $request->user();

        if (!$user->two_factor_secret) {
            return $this->errorResponse('2FA tidak diaktifkan.', 400);
        }

        $google2fa = new Google2FA();
        $secret = decrypt($user->two_factor_secret);
        $valid = $google2fa->verifyKey($secret, $validated['code']);

        if (!$valid) {
            throw ValidationException::withMessages([
                'code' => ['Kode 2FA tidak valid.'],
            ]);
        }

        $user->update([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ]);

        return $this->successResponse(null, '2FA berhasil dinonaktifkan.');
    }

    public function recoveryCodes(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->two_factor_recovery_codes) {
            return $this->errorResponse('2FA tidak diaktifkan.', 400);
        }

        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes));

        return $this->successResponse($recoveryCodes);
    }

    public function regenerateRecoveryCodes(Request $request): JsonResponse
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        if (!\Hash::check($request->password, $request->user()->password)) {
            throw ValidationException::withMessages([
                'password' => ['Password salah.'],
            ]);
        }

        $newCodes = collect(range(1, 8))->map(fn () => str()->random(10))->toArray();

        $request->user()->update([
            'two_factor_recovery_codes' => encrypt(json_encode($newCodes)),
        ]);

        return $this->successResponse($newCodes, 'Recovery codes berhasil digenerate ulang.');
    }
}