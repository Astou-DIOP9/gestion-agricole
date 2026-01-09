<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\RecolteService;
use App\Models\Variete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecolteController extends Controller
{
    protected $recolteService;

    public function __construct(RecolteService $recolteService)
    {
        $this->recolteService = $recolteService;
    }

    public function index(Request $request)
{
    $filters = $request->only(['date_debut', 'date_fin', 'id_variete', 'id_produit', 'qualite']);
    
    // Utilisez votre service qui retourne un QueryBuilder
    $query = $this->recolteService->getRecoltesAvecStatistiques($filters);
    
    // Pagination automatique
    $perPage = 10;
    $recoltes = $query->paginate($perPage);

    // Récupérer les variétés pour le filtre
    $varietes = DB::connection('oracle')->select("
        SELECT v.*, p.nom_produit 
        FROM VARIETE v 
        JOIN PRODUIT p ON v.id_produit = p.id_produit 
        ORDER BY v.nom_variete
    ");

    return view('recoltes.index', compact('recoltes', 'filters', 'varietes'));
}
    public function create()
    {
        $varietes = DB::connection('oracle')->select("
            SELECT v.*, p.nom_produit 
            FROM VARIETE v 
            JOIN PRODUIT p ON v.id_produit = p.id_produit 
            ORDER BY v.nom_variete
        ");

        return view('recoltes.create', compact('varietes'));
    }

    public function store(Request $request)
    {
        $result = $this->recolteService->enregistrerRecolte($request->all());

        if (!$result['success']) {
            return back()->withErrors($result['errors'] ?? [])
                         ->with('error', $result['error'] ?? 'Erreur lors de l\'enregistrement');
        }

        return redirect()->route('recoltes.index')
                         ->with('success', 'Récolte enregistrée avec succès !');
    }

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
                return redirect()->route('recoltes.index')
                                 ->with('error', 'Récolte non trouvée');
            }

            $ventes = DB::connection('oracle')->select("
                SELECT * FROM VENTE 
                WHERE id_recolte = ?
                ORDER BY date_vente DESC
            ", [$id]);

            $stock = DB::connection('oracle')->selectOne("
                SELECT * FROM STOCK 
                WHERE id_recolte = ?
            ", [$id]);

            return view('recoltes.show', compact('recolte', 'ventes', 'stock'));

        } catch (\Exception $e) {
            return redirect()->route('recoltes.index')
                             ->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function quantiteDisponible($id)
    {
        try {
            $quantite = DB::connection('oracle')->selectOne("
                SELECT F_QUANTITE_DISPONIBLE(?) as disponible FROM DUAL
            ", [$id]);

            return response()->json([
                'success' => true,
                'quantite' => $quantite->disponible ?? 0
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function statistiquesMensuelles(Request $request)
    {
        $mois = $request->input('mois', date('Y-m'));
        $stats = $this->recolteService->getStatistiquesMensuelles($mois);

        return view('recoltes.statistiques', compact('stats', 'mois'));
    }
}