<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\KycSubmissionRequest;
use Illuminate\Support\Facades\Auth;

class KycController extends Controller
{
    public function index()
    {
        $seller = Auth::user();

        return view('seller.kyc.verify', compact('seller'));
    }

    public function store(KycSubmissionRequest $request)
    {
        $seller    = Auth::user();
        $validated = $request->validated();

        // Upload foto KTP via spatie/medialibrary
        $seller->addMediaFromRequest('id_photo')
            ->toMediaCollection('kyc_id_photo');

        // Upload foto selfie via spatie/medialibrary
        $seller->addMediaFromRequest('selfie_photo')
            ->toMediaCollection('kyc_selfie');

        // Update status dan simpan data KYC ke meta
        $seller->update([
            'kyc_status' => 'pending',
            'meta'       => array_merge($seller->meta ?? [], [
                'kyc_full_name'    => $validated['full_name'],
                'kyc_id_number'    => $validated['id_number'],
                'kyc_bank_name'    => $validated['bank_name'],
                'kyc_bank_account' => $validated['bank_account'],
                'kyc_bank_holder'  => $validated['bank_holder'],
                'kyc_npwp'         => $validated['npwp_number'] ?? null,
                'kyc_submitted_at' => now()->toIso8601String(),
            ]),
        ]);

        return redirect()->route('seller.dashboard')
            ->with('success', 'Dokumen KYC berhasil dikirim. Tim kami akan memverifikasi dalam 1-2 hari kerja.');
    }
}
