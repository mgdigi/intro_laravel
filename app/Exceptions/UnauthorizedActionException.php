<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Exception;

class UnauthorizedActionException extends Exception
{
    use ApiResponse;

    public function render($request)
    {
        return $this->errorResponse('Action non autorisée', 403);
    }
}
