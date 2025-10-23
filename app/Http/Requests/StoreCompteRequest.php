<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompteRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
    }

    public function rules()
    {
        return [
            'type' => 'required|string|in:epargne,cheque',
            'solde' => 'required|numeric|min:10000',
            'devise' => 'required|string|in:FCFA,EUR,USD',
            'client' => 'required|array',
            'client.nom' => 'required|string|max:255',
            'client.prenom' => 'required|string|max:255',
            'client.password' => 'required|string|min:6',
            'client.email' => 'required|email|unique:clients,email',
            'client.telephone' => ['required', 'unique:clients,telephone', new \App\Rules\ValidSenegalPhone],
            'client.nci' => ['required', 'unique:clients,nci', new \App\Rules\ValidNci],
            'client.adresse' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'solde.min' => 'Le solde initial doit être supérieur ou égal à 10 000 FCFA.',
            'client.email.unique' => 'Cet email est déjà utilisé.',
            'client.telephone.unique' => 'Ce numéro de téléphone existe déjà.',
        ];
    }
}
