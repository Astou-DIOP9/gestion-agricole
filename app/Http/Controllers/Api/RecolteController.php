<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RecolteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class RecolteController extends Controller
{
    protected $recolteService;

    public function __construct(RecolteService $recolteService)
    {
        $this->recolteService = $recolteService;
    }

    /**
     * Liste des récoltes avec filtres
     */
    public function index(Request $request)
    {
        $filters = $request->only(['date_debut', 'date_fin', 'id_variete', 'id_produit', 'qualite']);
        $recoltes = $this->recolteService->getRecoltesAvecStatistiques($filters);

        return response()->json([
            'success' => true,
            'filters' => $filters,
            'count' => $recoltes->count(),
            'data' => $recoltes
        ]);
    }

    /**
     * Enregistrer une nouvelle récolte
     */
    public function store(Request $request)
    {
        $result = $this->recolteService->enregistrerRecolte($request->all());

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Erreur',
                'errors' => $result['errors'] ?? []
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Récolte enregistrée avec succès'
        ], 201);
    }

    /**
     * Détail d'une récolte
     */
    public function show($id)
    {
        try {
            $recolte = DB::connection('oracle')->selectOne("
                SELECT 
                    r.*,
                    v.nom_variete,
                    p.nom_produit,
                    F_QUANTITE_DISPONIBLE(r.id_recolte) as quantite_disponible,
                    (SELECT SUM(quantite_vendue) FROM VENTE WHERE id_recolte = r.id_recolte) as quantite_vendue,
                    (SELECT COUNT(*) FROM VENTE WHERE id_recolte = r.id_recolte) as nb_ventes
                FROM RECOLTE r
                JOIN VARIETE v ON r.id_variete = v.id_variete
                JOIN PRODUIT p ON v.id_produit = p.id_produit
                WHERE r.id_recolte = ?
            ", [$id]);

            if (!$recolte) {
                return response()->json([
                    'success' => false,
                    'message' => 'Récolte non trouvée'
                ], 404);
            }

            // Ventes associées
            $ventes = DB::connection('oracle')->select("
                SELECT * FROM VENTE 
                WHERE id_recolte = ?
                ORDER BY date_vente DESC
            ", [$id]);

            // Stock associé
            $stock = DB::connection('oracle')->selectOne("
                SELECT * FROM STOCK 
                WHERE id_recolte = ?
            ", [$id]);

            return response()->json([
                'success' => true,
                'data' => [
                    'recolte' => $recolte,
                    'ventes' => $ventes,
                    'stock' => $stock
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Statistiques mensuelles
     */
    public function statistiques(Request $request)
    {
        $mois = $request->input('mois', date('Y-m'));
        $stats = $this->recolteService->getStatistiquesMensuelles($mois);

        return response()->json([
            'success' => true,
            'mois' => $mois,
            'data' => $stats
        ]);
    }

    /**
     * Quantité disponible pour une récolte
     */
    public function quantiteDisponible($id)
    {
        try {
            $quantite = DB::connection('oracle')->selectOne("
                SELECT F_QUANTITE_DISPONIBLE(?) as disponible FROM DUAL
            ", [$id]);

            return response()->json([
                'success' => true,
                'id_recolte' => $id,
                'quantite_disponible' => $quantite->disponible ?? 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
