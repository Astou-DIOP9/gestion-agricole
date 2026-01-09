<?php

namespace App\Services;

use App\Repositories\OracleRepository;
use App\Models\Recolte;
use Illuminate\Support\Facades\DB;

class RecolteService
{
    protected $oracleRepository;

    public function __construct(OracleRepository $oracleRepository)
    {
        $this->oracleRepository = $oracleRepository;
    }

    /**
     * Enregistrer une nouvelle récolte via procédure Oracle
     */
    public function enregistrerRecolte(array $data)
    {
        // Valider les données
        $validator = validator($data, [
            'id_variete' => 'required|integer|exists:VARIETE,id_variete',
            'date_recolte' => 'required|date',
            'heure_recolte' => 'required|date_format:H:i',
            'quantite' => 'required|numeric|min:0.1',
            'qualite' => 'in:EXCELLENTE,BONNE,MOYENNE,FAIBLE'
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }

        // Exécuter la procédure Oracle
        return $this->oracleRepository->enregistrerRecolte($data);
    }

    /**
     * Liste des récoltes avec statistiques
     */
    public function getRecoltesAvecStatistiques($filters = [])
    {
        $query = DB::connection('oracle')->table('RECOLTE as r')
            ->join('VARIETE as v', 'r.id_variete', '=', 'v.id_variete')
            ->join('PRODUIT as p', 'v.id_produit', '=', 'p.id_produit')
            ->select(
                'r.id_recolte',
                'r.date_recolte',
                'r.quantite',
                'r.qualite',
                'v.nom_variete',
                'p.nom_produit',
                DB::raw("F_QUANTITE_DISPONIBLE(r.id_recolte) as quantite_disponible"),
                DB::raw("(SELECT SUM(quantite_vendue) FROM VENTE WHERE id_recolte = r.id_recolte) as quantite_vendue"),
                DB::raw("(SELECT COUNT(*) FROM VENTE WHERE id_recolte = r.id_recolte) as nb_ventes")
            );

        // Appliquer les filtres
        if (!empty($filters['date_debut'])) {
            $query->where('r.date_recolte', '>=', $filters['date_debut']);
        }
        if (!empty($filters['date_fin'])) {
            $query->where('r.date_recolte', '<=', $filters['date_fin']);
        }
        if (!empty($filters['id_variete'])) {
            $query->where('r.id_variete', $filters['id_variete']);
        }
        if (!empty($filters['id_produit'])) {
            $query->where('v.id_produit', $filters['id_produit']);
        }

        // Retournez le QueryBuilder, pas le résultat !
        return $query->orderBy('r.date_recolte', 'desc');
    }

    /**
     * Statistiques mensuelles
     */
    public function getStatistiquesMensuelles($mois = null)
    {
        if (!$mois) {
            $mois = date('Y-m');
        }

        return DB::connection('oracle')->select("
            SELECT * FROM V_STATISTIQUES_RECOLTES 
            WHERE mois = ?
            ORDER BY quantite_totale_kg DESC
        ", [$mois]);
    }
}
