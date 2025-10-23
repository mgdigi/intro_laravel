<?php

namespace App\Http\Controllers\Api\Compte;

use App\Http\Controllers\Controller;
use App\Models\Compte;
use App\Http\Resources\CompteResource;
use App\Models\Client;
use App\Models\User;
use App\Traits\ApiResponse;
use App\Http\Requests\StoreCompteRequest;
use App\Http\Requests\CompteIndexRequest;
use App\Http\Requests\UpdateCompteRequest;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Info(
 *     title="API Ges_Compte Laravel",
 *     version="1.0.0",
 *     description="API pour la gestion des comptes bancaires"
 * )
 * @OA\Tag(
 *     name="Comptes",
 *     description="Gestion des comptes bancaires"
 * )
 *
 * @OA\Schema(
 *     schema="PaginationMeta",
 *     title="PaginationMeta",
 *     description="Métadonnées de pagination",
 *     @OA\Property(property="currentPage", type="integer", description="Page actuelle"),
 *     @OA\Property(property="totalPages", type="integer", description="Nombre total de pages"),
 *     @OA\Property(property="totalItems", type="integer", description="Nombre total d'éléments"),
 *     @OA\Property(property="itemsPerPage", type="integer", description="Éléments par page"),
 *     @OA\Property(property="hasNext", type="boolean", description="Page suivante disponible"),
 *     @OA\Property(property="hasPrevious", type="boolean", description="Page précédente disponible")
 * ),
 * @OA\Schema(
 *     schema="PaginationLinks",
 *     title="PaginationLinks",
 *     description="Liens de pagination",
 *     @OA\Property(property="self", type="string", format="uri", description="Lien vers la page actuelle"),
 *     @OA\Property(property="next", type="string", format="uri", nullable=true, description="Lien vers la page suivante"),
 *     @OA\Property(property="first", type="string", format="uri", description="Lien vers la première page"),
 *     @OA\Property(property="last", type="string", format="uri", description="Lien vers la dernière page")
 * )
 */
class CompteController extends Controller
{

    use ApiResponse;

     /**
     * @OA\Get(
     *     path="/api/v1/comptes",
     *     tags={"Comptes"},
     *     summary="Lister tous les comptes",
     *     description="Récupère une liste paginée des comptes avec possibilité de filtrage et tri",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Numéro de la page",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Nombre d'éléments par page",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=100)
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Type de compte (epargne ou cheque)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"epargne", "cheque"})
     *     ),
     *     @OA\Parameter(
     *         name="statut",
     *         in="query",
     *         description="Statut du compte",
     *         required=false,
     *         @OA\Schema(type="string", enum={"actif", "bloque", "ferme"})
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Recherche par titulaire ou numéro de compte",
     *         required=false,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Champ de tri",
     *         required=false,
     *         @OA\Schema(type="string", enum={"dateCreation", "solde", "titulaire"})
     *     ),
     *     @OA\Parameter(
     *         name="order",
     *         in="query",
     *         description="Ordre de tri",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des comptes récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", nullable=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="comptes", type="array", @OA\Items(ref="#/components/schemas/CompteResource")),
     *                 @OA\Property(property="pagination", ref="#/components/schemas/PaginationMeta"),
     *                 @OA\Property(property="links", ref="#/components/schemas/PaginationLinks")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Paramètres de requête invalides",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
  public function index(CompteIndexRequest $request)
{

    $cacheKey =  'comptes_' . md5(json_encode($request->all()));

    $cacheData = Cache::get($cacheKey);

    if ($cacheData) {
        return $this->successResponse($cacheData);
    }

    $query = Compte::query();

    if ($request->filled('type')) {
        $query->where('type', $request->type);
    }

    if ($request->filled('statut')) {
        $query->where('statut', $request->statut);
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('titulaire', 'like', "%$search%")
              ->orWhere('numero_compte', 'like', "%$search%");
        });
    }

    $sortField = $request->get('sort', 'created_at');
    $order = $request->get('order', 'desc');
    if ($sortField === 'dateCreation') $sortField = 'created_at';
    $query->orderBy($sortField, $order);

    $limit = $request->get('limit', 10);
    $comptes = $query->paginate($limit)->appends($request->all());

    Cache::put($cacheKey, CompteResource::collection($comptes), now()->addMinutes(10));

    return $this->successResponse([
        CompteResource::collection($comptes),
        'pagination' => [
            'currentPage' => $comptes->currentPage(),
            'totalPages' => $comptes->lastPage(),
            'totalItems' => $comptes->total(),
            'itemsPerPage' => $comptes->perPage(),
            'hasNext' => $comptes->hasMorePages(),
            'hasPrevious' => $comptes->currentPage() > 1,
        ],
        'links' => [
            'self' => $comptes->url($comptes->currentPage()),
            'next' => $comptes->nextPageUrl(),
            'first' => $comptes->url(1),
            'last' => $comptes->url($comptes->lastPage()),
        ]
], 
);

}



     /**
     * @OA\Post(
     *     path="/api/v1/comptes",
     *     tags={"Comptes"},
     *     summary="Créer un nouveau compte",
     *     description="Crée un nouveau compte bancaire avec un client associé",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"type", "solde", "devise", "user"},
     *             @OA\Property(property="type", type="string", enum={"epargne", "cheque"}, example="epargne"),
     *             @OA\Property(property="solde", type="number", minimum=10000, example=50000),
     *             @OA\Property(property="devise", type="string", enum={"FCFA", "EUR", "USD"}, example="FCFA"),
     *             @OA\Property(property="user", type="object", required={"nom", "prenom", "password", "email", "telephone", "nci", "adresse"},
     *                 @OA\Property(property="nom", type="string", maxLength=255, example="Dupont"),
     *                 @OA\Property(property="prenom", type="string", maxLength=255, example="Jean"),
     *                 @OA\Property(property="password", type="string", minLength=6, example="password123"),
     *                 @OA\Property(property="email", type="string", format="email", example="jean.dupont@email.com"),
     *                 @OA\Property(property="telephone", type="string", example="+221771234567"),
     *                 @OA\Property(property="nci", type="string", example="1234567890123456"),
     *                 @OA\Property(property="adresse", type="string", example="Dakar, Sénégal")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Compte créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Compte créé avec succès"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string", format="uuid"),
     *                 @OA\Property(property="numeroCompte", type="string"),
     *                 @OA\Property(property="titulaire", type="string"),
     *                 @OA\Property(property="type", type="string"),
     *                 @OA\Property(property="solde", type="number"),
     *                 @OA\Property(property="devise", type="string"),
     *                 @OA\Property(property="dateCreation", type="string", format="date-time"),
     *                 @OA\Property(property="statut", type="string"),
     *                 @OA\Property(property="metadata", type="object",
     *                     @OA\Property(property="derniereModification", type="string", format="date-time"),
     *                     @OA\Property(property="version", type="integer")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Données de validation invalides",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
     public function store(StoreCompteRequest $request)
{
    $validated = $request->validated();



   $userData = $validated['user'];

   $user = User::where('email', $userData['email'])
               ->orWhere('telephone', $userData['telephone'])
               ->first();

   if (!$user) {
       $user = User::create([
           ...$userData,
           'password' => bcrypt($userData['password']),
       ]);

       // Créer le client associé
       Client::create([
           'user_id' => $user->id,
       ]);
   }

   $compte = Compte::create([
       'type' => $validated['type'],
       'solde' => $validated['solde'],
       'titulaire' => $user->nom . ' ' . $user->prenom,
       'devise' => $validated['devise'],
       'user_id' => $user->id,
       'statut' => 'actif',
   ]);

    return $this->successResponse([
        'id' => $compte->id,
        'numeroCompte' => $compte->numero_compte,
        'titulaire' => $user->nom . ' ' . $user->prenom,
        'type' => $compte->type,
        'solde' => $compte->solde,
        'devise' => $compte->devise,
        'dateCreation' => $compte->created_at,
        'statut' => $compte->statut,
        'metadata' => [
            'derniereModification' => $compte->updated_at,
            'version' => 1,
        ],
    ], 'Compte créé avec succès', 201);
}

     /**
     * @OA\Get(
     *     path="/api/v1/comptes/{compte}",
     *     tags={"Comptes"},
     *     summary="Afficher un compte spécifique",
     *     description="Récupère les détails d'un compte bancaire spécifique",
     *     @OA\Parameter(
     *         name="compte",
     *         in="path",
     *         description="ID du compte",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails du compte récupérés avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", nullable=true),
     *             @OA\Property(property="data", ref="#/components/schemas/CompteResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Compte non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Compte non trouvé")
     *         )
     *     )
     * )
     */
     public function show(Compte $compte)
{
    return $this->successResponse(new CompteResource($compte));
}

     /**
     * @OA\Patch(
     *     path="/api/v1/comptes/{compte}",
     *     tags={"Comptes"},
     *     summary="Mettre à jour un compte",
     *     description="Met à jour les informations d'un compte bancaire existant",
     *     @OA\Parameter(
     *         name="compte",
     *         in="path",
     *         description="ID du compte",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="titulaire", type="string", maxLength=150, example="Jean Dupont"),
     *             @OA\Property(property="informationsUser", type="object",
     *                 @OA\Property(property="nom", type="string", maxLength=50, example="Dupont"),
     *                 @OA\Property(property="prenom", type="string", maxLength=70, example="Jean"),
     *                 @OA\Property(property="email", type="string", format="email", example="jean.dupont@email.com"),
     *                 @OA\Property(property="telephone", type="string", example="+221771234567"),
     *                 @OA\Property(property="nci", type="string", example="1234567890123456"),
     *                 @OA\Property(property="adresse", type="string", example="Dakar, Sénégal"),
     *                 @OA\Property(property="password", type="string", minLength=6, example="newpassword123")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Compte mis à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Compte mis à jour avec succès"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string", format="uuid"),
     *                 @OA\Property(property="numeroCompte", type="string"),
     *                 @OA\Property(property="titulaire", type="string"),
     *                 @OA\Property(property="type", type="string"),
     *                 @OA\Property(property="solde", type="number"),
     *                 @OA\Property(property="devise", type="string"),
     *                 @OA\Property(property="dateCreation", type="string", format="date-time"),
     *                 @OA\Property(property="statut", type="string"),
     *                 @OA\Property(property="metadata", type="object",
     *                     @OA\Property(property="derniereModification", type="string", format="date-time"),
     *                     @OA\Property(property="version", type="integer")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Compte non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Compte non trouvé")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Données de validation invalides",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
     public function update(UpdateCompteRequest $request, Compte $compte)
{
    $validated = $request->validated();

    if(isset($validated['titulaire'])) {
        $compte->titulaire = $validated['titulaire'];
    }

    if(isset($validated['informationsUser'])) {
        $userData = $validated['informationsUser'];
        $user = $compte->user;

        if(isset($userData['telephone'])) $user->telephone = $userData['telephone'];
        if(isset($userData['email'])) $user->email = $userData['email'];
        if(isset($userData['password']) && $userData['password'] !== '') $user->password = bcrypt($userData['password']);
        if(isset($userData['nci'])) $user->nci = $userData['nci'];

        $user->save();
    }

    $compte->save();

    return $this->successResponse([
        'id' => $compte->id,
        'numeroCompte' => $compte->numero_compte,
        'titulaire' => $compte->titulaire,
        'type' => $compte->type,
        'solde' => $compte->solde,
        'devise' => $compte->devise,
        'dateCreation' => $compte->created_at,
        'statut' => $compte->statut,
        'metadata' => [
            'derniereModification' => $compte->updated_at,
            'version' => 1,
        ],
    ], 'Compte mis à jour avec succès', 201);
}




}
