<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidSenegalPhone implements Rule
{
    public function passes($attribute, $value)
    {
        return preg_match('/^(\+221)?7[05678][0-9]{7}$/', $value);
    }

    public function message()
    {
        return 'Le numéro de téléphone doit être valide (format sénégalais).';
    }
}
