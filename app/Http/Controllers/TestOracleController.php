<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\Variete;
use App\Services\DashboardService;
use Illuminate\Support\Facades\DB;

class TestOracleController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Test de connexion et données de base
     */
    public function testConnexion()
    {
        try {
            // 1. Test connexion
            $pdo = DB::connection('oracle')->getPdo();
            $tables = DB::connection('oracle')->select("SELECT table_name FROM user_tables");

            // 2. Données des modèles
            $produits = Produit::all();
            $varietes = Variete::with('produit')->get();

            // 3. Test des vues
            $stats = $this->dashboardService->getStatistiquesRecoltes(5);
            $stocks = $this->dashboardService->getEtatStocks();

            return response()->json([
                'status' => 'success',
                'database' => 'Oracle connecté',
                'tables_count' => count($tables),
                'tables' => $tables,
                'produits_count' => $produits->count(),
                'varietes_count' => $varietes->count(),
                'statistiques_recoltes' => $stats,
                'etat_stocks' => $stocks,
                'fonctions_oracle' => $this->dashboardService->testFonctionsOracle()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tester une procédure Oracle simple
     */
    public function testProcedure()
    {
        try {
            // Tester P_JOURNALISER_ACTION (la plus simple)
            DB::connection('oracle')->statement("
                BEGIN
                    P_JOURNALISER_ACTION('TEST_LARAVEL', 'Test depuis Laravel');
                END;
            ");

            // Vérifier que c'est bien enregistré
            $journal = DB::connection('oracle')->select("
                SELECT * FROM JOURNAL_ACTIONS 
                WHERE type_action = 'TEST_LARAVEL' 
                ORDER BY date_action DESC
            ");

            return response()->json([
                'status' => 'success',
                'message' => 'Procédure exécutée avec succès',
                'journal_entry' => $journal[0] ?? null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Données pour dashboard
     */
    public function dashboardData()
    {
        $mois = date('Y-m');

        return response()->json([
            'status' => 'success',
            'mois' => $mois,
            'statistiques' => $this->dashboardService->getStatistiquesRecoltes(),
            'chiffre_affaires' => $this->dashboardService->getChiffreAffaires($mois),
            'top_ventes' => $this->dashboardService->getTopVentes(5),
            'stocks' => $this->dashboardService->getEtatStocks()
        ]);
    }
}