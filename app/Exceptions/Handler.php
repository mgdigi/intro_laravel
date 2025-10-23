<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Exceptions\CompteNotFoundException;
use App\Models\Compte;

class Handler extends ExceptionHandler
{
   
    

public function register()
{
    $this->renderable(function (ModelNotFoundException $e, $request) {
        if ($e->getModel() === Compte::class) {
            throw new CompteNotFoundException();
        }
    });

    $this->renderable(function (\Illuminate\Database\QueryException $e, $request) {
        if (str_contains($e->getMessage(), 'invalid input syntax for type uuid')) {
            throw new CompteNotFoundException();
        }
    });
}



    
}
