<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\VenteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VenteController extends Controller
{
    protected $venteService;

    public function __construct(VenteService $venteService)
    {
        $this->venteService = $venteService;
    }

    public function index(Request $request)
    {
        try {
            $dateDebut = $request->input('date_debut', date('2025-12-01'));
            $dateFin = $request->input('date_fin', date('2026-4-d'));

            $ventes = DB::connection('oracle')->select("
                SELECT 
                    v.*,
                    TO_CHAR(v.date_vente, 'YYYY-MM-DD HH24:MI:SS') as date_vente_formatted,
                    r.date_recolte,
                    TO_CHAR(r.date_recolte, 'YYYY-MM-DD') as date_recolte_formatted,
                    var.nom_variete,
                    p.nom_produit,
                    (SELECT F_QUANTITE_DISPONIBLE(r.id_recolte) FROM DUAL) as quantite_restante
                FROM VENTE v
                JOIN RECOLTE r ON v.id_recolte = r.id_recolte
                JOIN VARIETE var ON r.id_variete = var.id_variete
                JOIN PRODUIT p ON var.id_produit = p.id_produit
                WHERE v.date_vente BETWEEN TO_DATE(?, 'YYYY-MM-DD') AND TO_DATE(?, 'YYYY-MM-DD')
                ORDER BY v.date_vente DESC
            ", [$dateDebut, $dateFin]);

            $totalCA = array_sum(array_column($ventes, 'montant_total'));
            $totalQuantite = array_sum(array_column($ventes, 'quantite_vendue'));

            return view('ventes.index', compact('ventes', 'dateDebut', 'dateFin', 'totalCA', 'totalQuantite'));
        } catch (\Exception $e) {
            Log::error('Erreur dans VenteController@index', ['error' => $e->getMessage()]);
            return back()->with('error', 'Erreur lors du chargement des ventes: ' . $e->getMessage());
        }
    }

    public function create(Request $request)
    {
        try {
            $recolteId = $request->input('recolte');

            // Récupérer les récoltes disponibles
            $recoltesDisponibles = DB::connection('oracle')->select("
                SELECT 
                    r.id_recolte,
                    TO_CHAR(r.date_recolte, 'YYYY-MM-DD') as date_recolte,
                    var.nom_variete,
                    p.nom_produit,
                    r.quantite,
                    F_QUANTITE_DISPONIBLE(r.id_recolte) as quantite_disponible,
                    NVL(pm.prix_kg, 0) as prix_marche
                FROM RECOLTE r
                JOIN VARIETE var ON r.id_variete = var.id_variete
                JOIN PRODUIT p ON var.id_produit = p.id_produit
                LEFT JOIN PRIX_MARCHE pm ON var.id_variete = pm.id_variete 
                    AND pm.date_prix = (SELECT MAX(date_prix) FROM PRIX_MARCHE WHERE id_variete = var.id_variete)
                WHERE F_QUANTITE_DISPONIBLE(r.id_recolte) > 0
                ORDER BY r.date_recolte DESC
            ");

            // Si une récolte spécifique est sélectionnée
            $recolteSelected = null;
            if ($recolteId) {
                foreach ($recoltesDisponibles as $recolte) {
                    if ($recolte->id_recolte == $recolteId) {
                        $recolteSelected = $recolte;
                        break;
                    }
                }
            }

            return view('ventes.create', compact('recoltesDisponibles', 'recolteSelected'));
        } catch (\Exception $e) {
            Log::error('Erreur dans VenteController@create', ['error' => $e->getMessage()]);
            return redirect()->route('ventes.index')
                ->with('error', 'Erreur lors du chargement du formulaire: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        Log::info('Vente store appelée', $request->all());

        // Validation des données
        $validated = $request->validate([
            'id_recolte' => 'required|integer',
            'date_vente' => 'required|date',
            'quantite_vendue' => 'required|numeric|min:0.1',
            'prix_unitaire' => 'required|numeric|min:0.01',
            'client_type' => 'required|in:PARTICULIER,REVENDEUR,RESTAURANT,AUTRE',
            'commentaire' => 'nullable|string|max:500'
        ]);

        // CORRECTION : Convertir le format datetime-local pour Oracle
        // Le champ date_vente est au format "2024-01-15T14:30"
        // Oracle attend "2024-01-15 14:30:00"
        if ($request->has('date_vente_oracle')) {
            $validated['date_vente'] = $request->input('date_vente_oracle');
        } else {
            // Fallback : convertir datetime-local
            $validated['date_vente'] = str_replace('T', ' ', $validated['date_vente']) . ':00';
        }

        // Calculer le montant total
        $validated['montant_total'] = $validated['quantite_vendue'] * $validated['prix_unitaire'];

        Log::info('Validation passée avec date Oracle', $validated);

        try {
            $result = $this->venteService->effectuerVente($validated);

            Log::info('Résultat du service', $result);

            if (!$result['success']) {
                $errors = isset($result['errors']) ? $result['errors']->all() : [$result['error']];
                return back()
                    ->withInput()
                    ->with('error', implode(', ', $errors));
            }

            return redirect()->route('ventes.index')
                ->with('success', 'Vente enregistrée avec succès ! ID: ' . ($result['vente_id'] ?? 'N/A'));
        } catch (\Exception $e) {
            Log::error('Erreur dans VenteController@store', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $validated
            ]);

            return back()
                ->withInput()
                ->with('error', 'Erreur système: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $vente = DB::connection('oracle')->selectOne("
                SELECT 
                    v.*,
                    TO_CHAR(v.date_vente, 'YYYY-MM-DD HH24:MI:SS') as date_vente_formatted,
                    r.date_recolte,
                    TO_CHAR(r.date_recolte, 'YYYY-MM-DD') as date_recolte_formatted,
                    r.quantite as quantite_recolte,
                    var.nom_variete,
                    p.nom_produit
                FROM VENTE v
                JOIN RECOLTE r ON v.id_recolte = r.id_recolte
                JOIN VARIETE var ON r.id_variete = var.id_variete
                JOIN PRODUIT p ON var.id_produit = p.id_produit
                WHERE v.id_vente = ?
            ", [$id]);

            if (!$vente) {
                return redirect()->route('ventes.index')
                    ->with('error', 'Vente non trouvée');
            }

            return view('ventes.show', compact('vente'));
        } catch (\Exception $e) {
            Log::error('Erreur dans VenteController@show', ['error' => $e->getMessage()]);
            return redirect()->route('ventes.index')
                ->with('error', 'Erreur lors du chargement de la vente: ' . $e->getMessage());
        }
    }

    public function chiffreAffaires(Request $request)
    {
        try {
            $request->validate([
                'date_debut' => 'required|date',
                'date_fin' => 'required|date|after_or_equal:date_debut'
            ]);

            $ca = $this->venteService->getChiffreAffaires(
                $request->date_debut,
                $request->date_fin
            );

            $totalCA = array_sum(array_column($ca, 'ca_total'));

            return view('ventes.chiffre-affaires', compact('ca', 'totalCA'));
        } catch (\Exception $e) {
            Log::error('Erreur dans VenteController@chiffreAffaires', ['error' => $e->getMessage()]);
            return back()->with('error', 'Erreur lors du calcul du CA: ' . $e->getMessage());
        }
    }

    public function topVentes(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            $topVentes = $this->venteService->getTopVentes($limit);

            return view('ventes.top', compact('topVentes', 'limit'));
        } catch (\Exception $e) {
            Log::error('Erreur dans VenteController@topVentes', ['error' => $e->getMessage()]);
            return back()->with('error', 'Erreur lors du chargement du top ventes: ' . $e->getMessage());
        }
    }
}
