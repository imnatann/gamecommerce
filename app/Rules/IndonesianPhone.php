<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IndonesianPhone implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $phone = preg_replace('/[\s\-()]/', '', (string) $value);

        if (! preg_match('/^(\+62|62|0)8[1-9][0-9]{6,10}$/', $phone)) {
            $fail('Nomor telepon tidak valid. Gunakan format Indonesia (08xx atau +628xx).');
        }
    }
}