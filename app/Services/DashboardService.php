<?php

namespace App\Services;

use App\Repositories\OracleRepository;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    protected $oracleRepository;

    public function __construct(OracleRepository $oracleRepository)
    {
        $this->oracleRepository = $oracleRepository;
    }

    /**
     * Récupérer tous les KPI pour le dashboard
     */
    public function getAllKPIs($mois = null)
    {
        if (!$mois) {
            $mois = date('Y-m');
        }

        return [
            'chiffre_affaires' => $this->getChiffreAffairesMensuel($mois),
            'quantite_recoltee' => $this->getQuantiteRecolteeMensuel($mois),
            'taux_invendus' => $this->getTauxInvendusMensuel($mois),
            'meilleure_variete' => $this->getMeilleureVarieteMois($mois),
            'alertes_stocks' => $this->countAlertesStocks(),
            'ventes_jour' => $this->getVentesDuJour(),
        ];
    }

    /**
     * Chiffre d'affaires mensuel
     */




    public function getChiffreAffairesMensuel($mois)
    {
        // $mois = "2025-12"
        list($annee, $mois_num) = explode('-', $mois);

        $result = DB::connection('oracle')->selectOne("
        SELECT COALESCE(SUM(montant_total), 0) as total
            FROM Projectbda1.VENTE
        WHERE EXTRACT(YEAR FROM date_vente) = ?
        AND EXTRACT(MONTH FROM date_vente) = ?
    ", [$annee, $mois_num]);

        return $result->total ?? 0;
    }

    /**
     * Quantité récoltée mensuelle
     */
    public function getQuantiteRecolteeMensuel($mois)
    {
        $result = DB::connection('oracle')->selectOne("
            SELECT SUM(quantite) as total
                FROM Projectbda1.RECOLTE
            WHERE TO_CHAR(date_recolte, 'YYYY-MM') = ?
        ", [$mois]);

        return $result->total ?? 0;
    }

    /**
     * Taux d'invendus mensuel
     */
    public function getTauxInvendusMensuel($mois)
    {
        $recolte = $this->getQuantiteRecolteeMensuel($mois);
        $ventes = $this->getChiffreAffairesMensuel($mois) / 2.5; // Estimation

        if ($recolte == 0) return 0;

        return round((($recolte - $ventes) / $recolte) * 100, 2);
    }



    /**
     * Meilleure variété du mois
     */
    public function getMeilleureVarieteMois($mois)
    {
        $result = DB::connection('oracle')->selectOne("
            SELECT nom_variete
            FROM (
                SELECT var.nom_variete, SUM(v.montant_total) as ca_total
                    FROM Projectbda1.VENTE v
                    JOIN Projectbda1.RECOLTE r ON v.id_recolte = r.id_recolte
                    JOIN Projectbda1.VARIETE var ON r.id_variete = var.id_variete
                WHERE TO_CHAR(v.date_vente, 'YYYY-MM') = ?
                GROUP BY var.nom_variete
                ORDER BY ca_total DESC
            ) WHERE ROWNUM = 1
        ", [$mois]);

        return $result->nom_variete ?? 'Aucune vente';
    }

    /**
     * Nombre d'alertes stocks
     */
    public function countAlertesStocks()
    {
        $result = DB::connection('oracle')->selectOne("
            SELECT COUNT(*) as count
                FROM Projectbda1.V_ETAT_STOCKS
            WHERE alerte_stock LIKE '%ALERTE%'
        ");

        return $result->count ?? 0;
    }

    /**
     * Ventes du jour
     */
    public function getVentesDuJour()
    {
        $result = DB::connection('oracle')->selectOne("
            SELECT SUM(montant_total) as total, COUNT(*) as count
                FROM Projectbda1.VENTE
            WHERE TRUNC(date_vente) = TRUNC(SYSDATE)
        ");

        return [
            'montant' => $result->total ?? 0,
            'nombre' => $result->count ?? 0
        ];
    }

    /**
     * Données pour graphique des récoltes
     */
    public function getRecoltesChartData($dateDebut, $dateFin)
    {
        return DB::connection('oracle')->select("
            SELECT 
                TRUNC(date_recolte) as date_recolte,
                SUM(quantite) as quantite_totale,
                COUNT(*) as nombre_recoltes
                FROM Projectbda1.RECOLTE
            WHERE date_recolte BETWEEN ? AND ?
            GROUP BY TRUNC(date_recolte)
            ORDER BY TRUNC(date_recolte)
        ", [$dateDebut, $dateFin]);
    }

    /**
     * Données pour graphique des ventes
     */
    public function getVentesChartData($dateDebut, $dateFin)
    {
        return DB::connection('oracle')->select("
            SELECT 
                TRUNC(date_vente) as date_vente,
                SUM(montant_total) as chiffre_affaires,
                SUM(quantite_vendue) as quantite_vendue,
                COUNT(*) as nombre_ventes
                FROM Projectbda1.VENTE
            WHERE date_vente BETWEEN ? AND ?
            GROUP BY TRUNC(date_vente)
            ORDER BY TRUNC(date_vente)
        ", [$dateDebut, $dateFin]);
    }

    /**
     * Top 5 variétés les plus vendues
     */
    public function getTopVarietes($limit = 5)
    {
        return DB::connection('oracle')->select("
                SELECT * FROM Projectbda1.V_TOP_VENTES 
            WHERE ROWNUM <= ?
            ORDER BY ca_total DESC
        ", [$limit]);
    }

    /**
     * Stocks nécessitant attention
     */
    public function getStocksAlerte($seuilJours = 7)
    {
        return DB::connection('oracle')->select("
            SELECT *
                FROM Projectbda1.V_ETAT_STOCKS
            WHERE quantite_stock_kg > 0
            AND (date_creation < SYSDATE - ? OR etat = 'PERIME')
            ORDER BY date_creation ASC
        ", [$seuilJours]);
    }
}
