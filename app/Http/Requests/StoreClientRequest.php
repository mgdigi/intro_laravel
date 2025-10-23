<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidNci;
use App\Rules\ValidSenegalPhone;
class StoreClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:50',
            'prenom' => 'required|string|max:70',
            'email' => 'required|email|unique:clients,email',
            'telephone' => [
            'required',
             ValidSenegalPhone::class,
            'unique:clients,telephone'
        ],
            'adresse' => 'required|string|max:150',
            'nci' =>   [
                'required',
                ValidNci::class,
                'unique:clients,nci'
            ]
        ];
    }
}
