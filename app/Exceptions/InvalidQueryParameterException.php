<?php

namespace App\Exceptions;

use Exception;
use App\Traits\ApiResponse;

class InvalidQueryParameterException extends Exception
{
    use ApiResponse;

    public function render($request)
    {
        return $this->errorResponse('Paramètre de requête invalide', 400);
    }
}
