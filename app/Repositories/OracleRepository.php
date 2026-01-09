<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OracleRepository
{
    /**
     * Exécuter une procédure Oracle stockée
     */
    public function executeProcedure(string $procedureName, array $params = [])
    {
        try {
            $placeholders = implode(',', array_fill(0, count($params), '?'));
            
            $result = DB::connection('oracle')->statement(
                "BEGIN {$procedureName}({$placeholders}); END;",
                $params
            );
            
            Log::info("Procédure {$procedureName} exécutée", ['params' => $params]);
            return ['success' => true, 'message' => 'Procédure exécutée avec succès'];
            
        } catch (\Exception $e) {
            Log::error("Erreur procédure {$procedureName}", [
                'error' => $e->getMessage(),
                'params' => $params
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Exécuter P_ENREGISTRER_RECOLTE
     */
    public function enregistrerRecolte(array $data)
    {
        return $this->executeProcedure('P_ENREGISTRER_RECOLTE', [
            $data['id_variete'],
            $data['date_recolte'],
            $data['heure_recolte'],
            $data['quantite'],
            $data['qualite'] ?? 'BONNE'
        ]);
    }

    /**
     * Exécuter P_EFFECTUER_VENTE
     */
    public function effectuerVente(array $data)
    {
        return $this->executeProcedure('P_EFFECTUER_VENTE', [
            $data['id_recolte'],
            $data['date_vente'],
            $data['quantite_vendue'],
            $data['prix_unitaire'],
            $data['client_type'] ?? 'PARTICULIER'
        ]);
    }

    /**
     * Exécuter une fonction Oracle et récupérer le résultat
     */
    public function executeFunction(string $functionName, array $params = [])
    {
        try {
            $placeholders = implode(',', array_fill(0, count($params), '?'));
            $query = "SELECT {$functionName}({$placeholders}) as result FROM DUAL";
            
            $result = DB::connection('oracle')->select($query, $params);
            return $result[0]->result ?? null;
            
        } catch (\Exception $e) {
            Log::error("Erreur fonction {$functionName}", ['error' => $e->getMessage()]);
            return null;
        }
    }
    

    /**
     * Récupérer les données d'un curseur REF CURSOR
     */
    public function getCursorData(string $packageName, string $procedureName, array $params = [])
    {
        try {
            // Pour Oracle, on doit utiliser une connexion PDO directe
            $pdo = DB::connection('oracle')->getPdo();
            
            // Préparer l'appel de la procédure
            $stmt = $pdo->prepare("BEGIN {$packageName}.{$procedureName}(:cursor); END;");
            
            // Déclarer le curseur
            $cursor = $pdo->prepare("SELECT 1 FROM DUAL");
            $stmt->bindParam(':cursor', $cursor, \PDO::PARAM_STMT);
            $stmt->execute();
            
            // Récupérer les données
            $data = [];
            do {
                $rowset = $cursor->fetchAll(\PDO::FETCH_ASSOC);
                if ($rowset) {
                    $data = array_merge($data, $rowset);
                }
            } while ($cursor->nextRowset());
            
            $cursor->closeCursor();
            return $data;
            
        } catch (\Exception $e) {
            Log::error("Erreur curseur {$packageName}.{$procedureName}", ['error' => $e->getMessage()]);
            return [];
        }
    }
    
}