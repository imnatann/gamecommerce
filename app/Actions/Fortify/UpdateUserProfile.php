<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfile implements UpdatesUserProfileInformation
{
    public function update($user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['sometimes', 'string', 'max:20'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
        ])->validate();

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
        ])->save();
    }
}