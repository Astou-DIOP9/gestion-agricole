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
        Log::info('=== VENTE SERVICE DÉBUT ===', $data);

        try {
            // Vérifier la quantité disponible
            $quantiteDisponible = $this->getQuantiteDisponible($data['id_recolte']);

            Log::info('Quantité disponible:', ['disponible' => $quantiteDisponible]);

            if ($data['quantite_vendue'] > $quantiteDisponible) {
                Log::warning('Quantité insuffisante', [
                    'demandée' => $data['quantite_vendue'],
                    'disponible' => $quantiteDisponible
                ]);

                return [
                    'success' => false,
                    'error' => "Quantité insuffisante. Disponible: {$quantiteDisponible} kg"
                ];
            }

            // Calculer le montant total
            $montantTotal = $data['quantite_vendue'] * $data['prix_unitaire'];

            Log::info('Calcul montant total:', [
                'quantite' => $data['quantite_vendue'],
                'prix' => $data['prix_unitaire'],
                'total' => $montantTotal
            ]);

            // Formater les données pour l'insertion
            $venteData = [
                'id_recolte' => $data['id_recolte'],
                'date_vente' => $data['date_vente'],
                'quantite_vendue' => $data['quantite_vendue'],
                'prix_unitaire' => $data['prix_unitaire'],
                'montant_total' => $montantTotal,
                'client_type' => $data['client_type'] ?? 'PARTICULIER',
                'commentaire' => $data['commentaire'] ?? null
            ];

            Log::info('Données préparées pour insertion:', $venteData);

            // Insérer la vente
            $venteId = $this->insererVente($venteData);

            if ($venteId) {
                Log::info('Vente insérée avec succès', ['id_vente' => $venteId]);

                return [
                    'success' => true,
                    'vente_id' => $venteId
                ];
            } else {
                Log::error('Échec de l\'insertion - pas d\'ID retourné');

                return [
                    'success' => false,
                    'error' => 'Erreur lors de l\'enregistrement de la vente'
                ];
            }
        } catch (\Exception $e) {
            Log::error('ERREUR VenteService:', [
                'message' => $e->getMessage(),
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
            Log::info('Vérification quantité disponible pour récolte:', ['id_recolte' => $idRecolte]);

            $result = DB::connection('oracle')->selectOne("
                SELECT F_QUANTITE_DISPONIBLE(?) as disponible FROM DUAL
            ", [$idRecolte]);

            Log::info('Résultat quantité disponible:', ['result' => $result]);

            return $result->disponible ?? 0;
        } catch (\Exception $e) {
            Log::error('Erreur getQuantiteDisponible:', [
                'id_recolte' => $idRecolte,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Insérer une vente dans la base - CORRECTION DU FORMAT DE DATE
     */
    private function insererVente(array $data)
    {
        try {
            // CORRECTION DU FORMAT DE DATE
            $dateOracle = $this->formatDateForOracle($data['date_vente']);

            Log::info('Date formatée pour Oracle:', [
                'original' => $data['date_vente'],
                'formatted' => $dateOracle
            ]);

            // Vérifier le format
            if (!$this->isValidOracleDate($dateOracle)) {
                throw new \Exception("Format de date invalide pour Oracle: {$dateOracle}");
            }

            // INSERT direct avec le bon format
            Log::info('Exécution INSERT INTO VENTE', [
                'sql_params' => [
                    'id_recolte' => $data['id_recolte'],
                    'date_vente' => $dateOracle,
                    'quantite_vendue' => $data['quantite_vendue'],
                    'prix_unitaire' => $data['prix_unitaire'],
                    'montant_total' => $data['montant_total'],
                    'client_type' => $data['client_type'],
                    'commentaire' => $data['commentaire']
                ]
            ]);

            // Option 1: INSERT direct avec TO_DATE
            DB::connection('oracle')->statement("
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
                $dateOracle,
                $data['quantite_vendue'],
                $data['prix_unitaire'],
                $data['montant_total'],
                $data['client_type'],
                $data['commentaire'] ?? null
            ]);

            Log::info('INSERT réussi, récupération de l\'ID...');

            // Récupérer l'ID de la vente insérée
            $result = DB::connection('oracle')->selectOne("
                SELECT id_vente 
                FROM VENTE 
                WHERE id_recolte = ? 
                AND date_vente = TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS')
                AND ROWNUM = 1
                ORDER BY id_vente DESC
            ", [$data['id_recolte'], $dateOracle]);

            Log::info('ID vente récupéré:', ['id_vente' => $result->id_vente ?? null]);

            return $result->id_vente ?? null;
        } catch (\Exception $e) {
            Log::error('ERREUR insererVente détaillée:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
                'formatted_date' => $dateOracle ?? 'N/A'
            ]);
            throw $e;
        }
    }

    /**
     * Formate une date pour Oracle (correction du problème)
     */
    private function formatDateForOracle($dateString)
    {
        Log::info('formatDateForOracle input:', ['date' => $dateString]);

        // 1. Si c'est un format datetime-local (2025-12-28T22:33)
        if (strpos($dateString, 'T') !== false) {
            // Remplacer T par espace
            $dateString = str_replace('T', ' ', $dateString);

            // Si pas de secondes, ajouter :00
            if (substr_count($dateString, ':') == 1) {
                $dateString .= ':00';
            }
        }

        // 2. Retirer les millisecondes si présentes
        // Format: YYYY-MM-DD HH:MM:SS:XX
        $parts = explode(':', $dateString);
        if (count($parts) > 3) {
            // Garder seulement heures:minutes:secondes
            $dateString = $parts[0] . ':' . $parts[1] . ':' . $parts[2];
        }

        // 3. S'assurer qu'on a exactement le format YYYY-MM-DD HH:MM:SS
        // Extraire date et heure
        $dateTimeParts = explode(' ', $dateString);

        if (count($dateTimeParts) !== 2) {
            // Si pas d'heure, ajouter minuit
            $dateString .= ' 00:00:00';
        } else {
            // Vérifier le format de l'heure
            $timeParts = explode(':', $dateTimeParts[1]);
            if (count($timeParts) === 1) {
                $dateString .= ':00:00';
            } elseif (count($timeParts) === 2) {
                $dateString .= ':00';
            }
        }

        // 4. Valider le format final
        $formatted = date('Y-m-d H:i:s', strtotime($dateString));

        Log::info('formatDateForOracle output:', ['formatted' => $formatted]);

        return $formatted;
    }

    /**
     * Vérifie si une date est dans un format valide pour Oracle
     */
    private function isValidOracleDate($dateString)
    {
        // Format attendu: YYYY-MM-DD HH:MM:SS
        $pattern = '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/';

        if (!preg_match($pattern, $dateString)) {
            Log::warning('Format de date invalide:', ['date' => $dateString]);
            return false;
        }

        // Vérifier que la date est valide
        $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $dateString);
        $isValid = $dateTime && $dateTime->format('Y-m-d H:i:s') === $dateString;

        Log::info('Validation date Oracle:', [
            'date' => $dateString,
            'is_valid' => $isValid
        ]);

        return $isValid;
    }

    /**
     * Chiffre d'affaires par période
     */
    public function getChiffreAffaires($dateDebut, $dateFin)
    {
        try {
            $results = DB::connection('oracle')->select("
                SELECT 
                    TO_CHAR(date_vente, 'YYYY-MM-DD') as jour,
                    SUM(montant_total) as ca_total,
                    SUM(quantite_vendue) as quantite_totale,
                    COUNT(*) as nb_ventes
                FROM VENTE
                WHERE date_vente BETWEEN TO_DATE(?, 'YYYY-MM-DD') AND TO_DATE(?, 'YYYY-MM-DD')
                GROUP BY TO_CHAR(date_vente, 'YYYY-MM-DD')
                ORDER BY jour
            ", [$dateDebut, $dateFin]);

            return $results;
        } catch (\Exception $e) {
            Log::error('Erreur getChiffreAffaires:', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /** 
     * Top des meilleures ventes
     */
    public function getTopVentes($limit = 10)
    {
        try {
            $results = DB::connection('oracle')->select("
                SELECT * FROM V_TOP_VENTES 
                WHERE ROWNUM <= ?
                ORDER BY ca_total DESC
            ", [$limit]);

            return $results;
        } catch (\Exception $e) {
            Log::error('Erreur getTopVentes:', ['error' => $e->getMessage()]);
            return [];
        }
    }
}
