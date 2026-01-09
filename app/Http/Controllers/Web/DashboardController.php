<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use App\Models\Vente;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(Request $request)
    {
        $mois = $request->input('mois', date('Y-m'));
        $dateDebut = $request->input('date_debut', date('Y-m-01'));
        $dateFin = $request->input('date_fin', date('Y-m-d'));


        // Récupérer toutes les données
        $kpis = $this->dashboardService->getAllKPIs($mois);
        $topVarietes = $this->dashboardService->getTopVarietes(5);
        $alertesStocks = $this->dashboardService->getStocksAlerte(7);
        $ventesJour = $this->dashboardService->getVentesDuJour();

        // Données pour graphiques - avec les corrections
        $recoltesChart = $this->dashboardService->getRecoltesChartData($dateDebut, $dateFin);
        $ventesChart = $this->dashboardService->getVentesChartData($dateDebut, $dateFin);
        // Récupérer toutes les ventes pour la vue
        $ventes = Vente::all();

        return view('dashboard.index', compact(
            'kpis',
            'topVarietes',
            'alertesStocks',
            'ventesJour',
            'recoltesChart',
            'ventesChart',
            'mois',
            'dateDebut',
            'dateFin',
            'ventes'
        ));
    }


    public function filter(Request $request)
    {
        $request->validate([
            'mois' => 'sometimes|date_format:Y-m',
            'date_debut' => 'sometimes|date',
            'date_fin' => 'sometimes|date|after_or_equal:date_debut'
        ]);

        return $this->index($request);
    }
}
