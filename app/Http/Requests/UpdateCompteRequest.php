<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $clientId = $this->compte->client_id ?? null;

        return [
            "client" => "sometimes|array",
            "client.nom" => "sometimes|string|max:50",
            "client.prenom" => "sometimes|string|max:70",
            "client.email" => "sometimes|email|unique:clients,email," . $clientId,
            "client.telephone" => ['sometimes', 'string', 'unique:clients,telephone,' . $clientId, new \App\Rules\ValidSenegalPhone],
            "client.nci" => ['sometimes', 'string', 'unique:clients,nci,' . $clientId, new \App\Rules\ValidNci],
            "client.adresse" => "sometimes|string",
            "titulaire" => "sometimes|string|max:150",
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $this->all();

            if (empty($data['titulaire']) && empty($data['client'])) {
                $validator->errors()->add('empty', 'Vous devez fournir au moins un champ à modifier.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'client.email.unique' => "Cet email est déjà utilisé.",
            'client.telephone.unique' => "Ce numéro de téléphone est déjà utilisé.",
            'client.nci.unique' => "Ce NCI est déjà utilisé.",
        ];
    }
}
