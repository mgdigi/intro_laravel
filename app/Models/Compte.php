<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @OA\Schema(
 *     schema="Compte",
 *     title="Compte",
 *     description="Modèle représentant un compte bancaire",
 *     @OA\Property(property="id", type="string", format="uuid", description="ID unique du compte"),
 *     @OA\Property(property="numero_compte", type="string", description="Numéro unique du compte"),
 *     @OA\Property(property="user_id", type="string", format="uuid", description="ID de l'utilisateur propriétaire"),
 *     @OA\Property(property="titulaire", type="string", maxLength=100, description="Nom du titulaire du compte"),
 *     @OA\Property(property="type", type="string", enum={"epargne", "cheque"}, description="Type de compte"),
 *     @OA\Property(property="solde", type="number", format="decimal", description="Solde du compte"),
 *     @OA\Property(property="devise", type="string", maxLength=10, description="Devise du compte"),
 *     @OA\Property(property="statut", type="string", enum={"actif", "bloque", "ferme"}, description="Statut du compte"),
 *     @OA\Property(property="derniere_modification", type="string", format="date-time", nullable=true, description="Date de dernière modification"),
 *     @OA\Property(property="version", type="integer", description="Version du compte"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Date de création"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Date de dernière mise à jour")
 * )
 */
class Compte extends BaseModel
{
    use HasFactory;

    protected $table = 'comptes';


    protected $fillable = [
        'id',
        'numero_compte',
        'user_id',
        'titulaire',
        'type',
        'solde',
        'devise',
        'statut',
        'derniere_modification',
        'version'
    ];

    protected function numeroCompte(): Attribute
    {
        return Attribute::make(
            set: fn($value) => $value ?: 'ACC-' . strtoupper(Str::random(10))
        );
    }

   

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id',  'id');
    }
}
