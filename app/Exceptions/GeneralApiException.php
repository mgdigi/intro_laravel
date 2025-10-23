<?php

namespace App\Exceptions;

use Exception;
use App\Traits\ApiResponse;

class GeneralApiException extends Exception
{
    use ApiResponse;
    public function render($request)
    {
        return $this->errorResponse('Une erreur est survenue lors du traitement de la requête', 500);
    }
}
