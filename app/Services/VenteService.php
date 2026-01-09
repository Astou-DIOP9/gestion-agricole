<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VenteService
{
    /**
     * Effectuer une vente
     */
    public function effectuerVente(array $data)
    {
        Log::info('VenteService: effectuerVente appelée', $data);

        try {
            // Vérifier la quantité disponible
            $quantiteDisponible = $this->getQuantiteDisponible($data['id_recolte']);

            Log::info('Quantité disponible: ' . $quantiteDisponible);

            if ($data['quantite_vendue'] > $quantiteDisponible) {
                return [
                    'success' => false,
                    'error' => "Quantité insuffisante. Disponible: {$quantiteDisponible} kg"
                ];
            }

            // Calculer le montant total
            $montantTotal = $data['quantite_vendue'] * $data['prix_unitaire'];

            Log::info('Montant total calculé: ' . $montantTotal);

            // Insérer la vente
            $venteId = $this->insererVente([
                'id_recolte' => $data['id_recolte'],
                'date_vente' => $data['date_vente'],
                'quantite_vendue' => $data['quantite_vendue'],
                'prix_unitaire' => $data['prix_unitaire'],
                'montant_total' => $montantTotal,
                'client_type' => $data['client_type'] ?? 'PARTICULIER',
                'commentaire' => $data['commentaire'] ?? null
            ]);

            Log::info('Vente insérée avec ID: ' . $venteId);

            return [
                'success' => true,
                'vente_id' => $venteId
            ];
        } catch (\Exception $e) {
            Log::error('Erreur VenteService: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);

            return [
                'success' => false,
                'error' => 'Erreur système: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Récupérer la quantité disponible pour une récolte
     */
    private function getQuantiteDisponible($idRecolte)
    {
        try {
            $result = DB::connection('oracle')->selectOne("
                SELECT F_QUANTITE_DISPONIBLE(?) as disponible FROM DUAL
            ", [$idRecolte]);

            return $result->disponible ?? 0;
        } catch (\Exception $e) {
            Log::error('Erreur getQuantiteDisponible: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Insérer une vente dans la base
     */
    private function insererVente(array $data)
    {
        try {
            $dateVente = str_replace('T', ' ', $data['date_vente']);

            Log::info('Tentative INSERT direct', [
                'data' => $data,
                'date_formatted' => $dateVente
            ]);

            // TEST : INSERT simple
            DB::connection('oracle')->insert("
            INSERT INTO VENTE (
                id_recolte, 
                date_vente, 
                quantite_vendue, 
                prix_unitaire, 
                montant_total, 
                client_type, 
                commentaire
            ) VALUES (?, TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS'), ?, ?, ?, ?, ?)
        ", [
                $data['id_recolte'],
                $dateVente,
                $data['quantite_vendue'],
                $data['prix_unitaire'],
                $data['montant_total'],
                $data['client_type'],
                $data['commentaire'] ?? null
            ]);

            Log::info('INSERT direct réussi');

            // Récupérer l'ID
            $result = DB::connection('oracle')->selectOne("
            SELECT id_vente 
            FROM VENTE 
            WHERE id_recolte = ? 
            AND date_vente = TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS')
            ORDER BY id_vente DESC
        ", [$data['id_recolte'], $dateVente]);

            return $result->id_vente ?? null;
        } catch (\Exception $e) {
            Log::error('Erreur INSERT direct: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Récupérer le chiffre d'affaires entre deux dates
     */
    public function getChiffreAffaires($dateDebut, $dateFin)
    {
        try {
            $ca = DB::connection('oracle')->select("
                SELECT 
                    var.nom_variete,
                    p.nom_produit,
                    SUM(v.quantite_vendue) as total_quantite,
                    SUM(v.montant_total) as ca_total,
                    COUNT(v.id_vente) as nombre_ventes
                FROM VENTE v
                JOIN RECOLTE r ON v.id_recolte = r.id_recolte
                JOIN VARIETE var ON r.id_variete = var.id_variete
                JOIN PRODUIT p ON var.id_produit = p.id_produit
                WHERE v.date_vente BETWEEN TO_DATE(?, 'YYYY-MM-DD') AND TO_DATE(?, 'YYYY-MM-DD') + INTERVAL '1' DAY
                GROUP BY var.nom_variete, p.nom_produit
                ORDER BY ca_total DESC
            ", [$dateDebut, $dateFin]);

            return $ca;
        } catch (\Exception $e) {
            Log::error('Erreur getChiffreAffaires: ' . $e->getMessage());
            return [];
        }
    }

    /** 
     * Top des meilleures ventes (facultatif)
     */
    public function getTopVentes($limit = 10)
    {
        // À implémenter si nécessaire
        return [];
    }
}
