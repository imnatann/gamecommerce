<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;

class KycSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && $user->isSeller() && ! $user->isKycVerified();
    }

    public function rules(): array
    {
        return [
            'full_name'    => ['required', 'string', 'max:255'],
            'id_number'    => ['required', 'string', 'size:16'],
            'id_photo'     => ['required', 'image', 'max:5120', 'mimes:jpg,jpeg,png'],
            'selfie_photo' => ['required', 'image', 'max:5120', 'mimes:jpg,jpeg,png'],
            'bank_name'    => ['required', 'string', 'max:100'],
            'bank_account' => ['required', 'string', 'max:50'],
            'bank_holder'  => ['required', 'string', 'max:255'],
            'npwp_number'  => ['nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_number.size'          => 'Nomor KTP harus tepat 16 digit.',
            'id_photo.required'       => 'Foto KTP wajib diupload.',
            'selfie_photo.required'   => 'Foto selfie dengan KTP wajib diupload.',
        ];
    }
}
