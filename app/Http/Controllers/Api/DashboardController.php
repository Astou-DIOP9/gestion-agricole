<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function kpis(Request $request)
    {
        $mois = $request->input('mois', date('Y-m'));

        return response()->json([
            'success' => true,
            'mois' => $mois,
            'data' => $this->dashboardService->getAllKPIs($mois)
        ]);
    }

    public function statistiques(Request $request)
    {
        $request->validate([
            'type' => 'sometimes|in:recoltes,ventes',
            'date_debut' => 'sometimes|date',
            'date_fin' => 'sometimes|date|after_or_equal:date_debut'
        ]);

        $type = $request->input('type', 'recoltes');
        $dateDebut = $request->input('date_debut', date('Y-m-01'));
        $dateFin = $request->input('date_fin', date('Y-m-d'));

        $data = match ($type) {
            'recoltes' => $this->dashboardService->getRecoltesChartData($dateDebut, $dateFin),
            'ventes' => $this->dashboardService->getVentesChartData($dateDebut, $dateFin),
            default => []
        };

        return response()->json([
            'success' => true,
            'type' => $type,
            'periode' => ['debut' => $dateDebut, 'fin' => $dateFin],
            'data' => $data
        ]);
    }

    public function alertesStocks(Request $request)
    {
        $seuilJours = $request->input('seuil_jours', 7);

        return response()->json([
            'success' => true,
            'seuil_jours' => $seuilJours,
            'data' => $this->dashboardService->getStocksAlerte($seuilJours)
        ]);
    }

    public function topVarietes(Request $request)
    {
        $limit = $request->input('limit', 5);

        return response()->json([
            'success' => true,
            'limit' => $limit,
            'data' => $this->dashboardService->getTopVarietes($limit)
        ]);
    }

    public function ventesJour()
    {
        return response()->json([
            'success' => true,
            'date' => date('Y-m-d'),
            'data' => $this->dashboardService->getVentesDuJour()
        ]);
    }
}
