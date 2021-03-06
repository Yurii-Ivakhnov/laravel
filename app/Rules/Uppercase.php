<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Uppercase implements Rule
{
    public function passes($attribute, $value): bool
    {
        return strtoupper($value) === $value;
    }

    public function message(): string
    {
        return ':attribute должно быть в верхнем регистре';
    }
}
