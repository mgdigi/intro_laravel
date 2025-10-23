<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CompteResource",
 *     title="CompteResource",
 *     description="Ressource représentant un compte bancaire avec ses métadonnées",
 *     @OA\Property(property="id", type="string", format="uuid", description="ID unique du compte"),
 *     @OA\Property(property="numeroCompte", type="string", description="Numéro unique du compte"),
 *     @OA\Property(property="titulaire", type="string", description="Nom du titulaire du compte"),
 *     @OA\Property(property="type", type="string", enum={"epargne", "cheque"}, description="Type de compte"),
 *     @OA\Property(property="solde", type="number", format="decimal", description="Solde du compte"),
 *     @OA\Property(property="devise", type="string", description="Devise du compte"),
 *     @OA\Property(property="dateCreation", type="string", format="date-time", description="Date de création du compte"),
 *     @OA\Property(property="statut", type="string", enum={"actif", "bloque", "ferme"}, description="Statut du compte"),
 *     @OA\Property(property="motifBlocage", type="string", nullable=true, description="Motif de blocage si applicable"),
 *     @OA\Property(property="metadata", type="object", description="Métadonnées du compte",
 *         @OA\Property(property="derniereModification", type="string", format="date-time", description="Date de dernière modification"),
 *         @OA\Property(property="version", type="integer", description="Version du compte")
 *     )
 * )
 */
class CompteResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'numeroCompte' => $this->numero_compte,
            'titulaire' => $this->titulaire,
            'type' => $this->type,
            'solde' => $this->solde,
            'devise' => $this->devise,
            'dateCreation' => $this->created_at,
            'statut' => $this->statut,
            'motifBlocage' => $this->motif_blocage ?? null,
            'metadata' => [
                'derniereModification' => $this->updated_at,
                'version' => $this->version,
            ]
        ];
    }
}
