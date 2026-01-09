<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $stocks = DB::connection('oracle')->select("
            SELECT 
                s.*,
                r.date_recolte,
                v.nom_variete,
                p.nom_produit,
                F_QUANTITE_DISPONIBLE(r.id_recolte) as quantite_disponible,
                CASE 
                    WHEN s.etat = 'PERIME' THEN 'PERIME'
                    WHEN s.date_creation < SYSDATE - 7 THEN 'ANCIEN'
                    WHEN s.quantite_restante < 10 THEN 'FAIBLE'
                    ELSE 'NORMAL'
                END as statut
            FROM STOCK s
            JOIN RECOLTE r ON s.id_recolte = r.id_recolte
            JOIN VARIETE v ON r.id_variete = v.id_variete
            JOIN PRODUIT p ON v.id_produit = p.id_produit
            WHERE s.quantite_restante > 0
            ORDER BY s.date_creation DESC
        ");

        $stats = [
            'total_stock' => array_sum(array_column($stocks, 'quantite_restante')),
            'nombre_lots' => count($stocks),
            'stock_perime' => count(array_filter($stocks, fn($s) => $s->statut === 'PERIME')),
            'stock_ancien' => count(array_filter($stocks, fn($s) => $s->statut === 'ANCIEN')),
        ];

        return view('stocks.index', compact('stocks', 'stats'));
    }

    public function alertes()
    {
        $alertes = $this->dashboardService->getStocksAlerte(7);
        $topVarietes = $this->dashboardService->getTopVarietes(10);

        return view('stocks.alertes', compact('alertes', 'topVarietes'));
    }
}
