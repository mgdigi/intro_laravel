<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidNci implements Rule
{
    public function passes($attribute, $value)
    {
        return preg_match('/^[0-9]{13}$/', $value);
    }

    public function message()
    {
        return 'Le NCI doit contenir 13 chiffres valides.';
    }
}
