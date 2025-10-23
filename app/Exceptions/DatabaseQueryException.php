<?php

namespace App\Exceptions;
use App\Traits\ApiResponse;

use Exception;

class DatabaseQueryException extends Exception
{
    use ApiResponse;

    public function render($request)
    {
        return $this->errorResponse('Erreur lors de l\'exécution de la requête en base de données', 500);
    }
}
