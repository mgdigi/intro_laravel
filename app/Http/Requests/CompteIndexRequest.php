<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class CompteIndexRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
    }

    public function rules()
    {
        return [
            'page' => 'integer|min:1',
            'limit' => 'integer|min:1|max:100',
            'type' => 'in:epargne,cheque',
            'statut' => 'in:actif,bloque,ferme',
            'search' => 'string|max:255',
            'sort' => 'in:dateCreation,solde,titulaire',
            'order' => 'in:asc,desc',
        ];
    }

    public function messages()
    {
        return [
            'type.in' => 'Le type doit être epargne ou cheque.',
            'statut.in' => 'Le statut doit être actif, bloque ou ferme.',
            'sort.in' => 'Le tri doit être dateCreation, solde ou titulaire.',
            'order.in' => 'L’ordre doit être asc ou desc.',
        ];
    }

    protected function failedValidation( $validator)
    {
        throw new \App\Exceptions\InvalidQueryParameterException($validator->errors());
    }
}
