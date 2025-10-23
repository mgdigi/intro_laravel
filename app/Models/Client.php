<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

use Illuminate\Support\Str;

/**
 * @OA\Schema(
 *     schema="Client",
 *     title="Client",
 *     description="Modèle représentant un client bancaire (relation avec User)",
 *     @OA\Property(property="id", type="string", format="uuid", description="ID unique du client"),
 *     @OA\Property(property="user_id", type="string", format="uuid", description="ID de l'utilisateur associé"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Date de création"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Date de dernière mise à jour")
 * )
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends BaseModel
{
    use HasFactory;

    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    } 
    
}

