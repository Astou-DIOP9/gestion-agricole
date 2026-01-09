
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\RecolteController;
use App\Http\Controllers\Api\VenteController;

// ==================== DASHBOARD ====================
Route::prefix('dashboard')->group(function () {
    Route::get('/kpis', [DashboardController::class, 'kpis']);
    Route::get('/statistiques', [DashboardController::class, 'statistiques']);
    Route::get('/alertes-stocks', [DashboardController::class, 'alertesStocks']);
    Route::get('/top-varietes', [DashboardController::class, 'topVarietes']);
    Route::get('/ventes-jour', [DashboardController::class, 'ventesJour']);
});

// ==================== RÉCOLTES ====================
Route::prefix('recoltes')->name('api.recoltes.')->group(function() {
    Route::get('/', [RecolteController::class, 'index'])->name('index');
    Route::post('/', [RecolteController::class, 'store'])->name('store');
    Route::get('/{id}', [RecolteController::class, 'show'])->name('show');
    Route::get('/{id}/quantite-disponible', [RecolteController::class, 'quantiteDisponible'])->name('quantite-disponible');
    Route::get('/statistiques/mensuelles', [RecolteController::class, 'statistiques'])->name('statistiques.mensuelles');
});
// ==================== VENTES ====================
Route::post('ventes', [VenteController::class, 'store']);
Route::get('ventes/chiffre-affaires', [VenteController::class, 'chiffreAffaires']);
Route::get('ventes/top', [VenteController::class, 'topVentes']);

// ==================== PRODUITS ET VARIÉTÉS ====================
Route::get('produits', function () {
    return response()->json([
        'success' => true,
        'data' => DB::connection('oracle')->select("SELECT * FROM PRODUIT ORDER BY nom_produit")
    ]);
});

Route::get('varietes', function () {
    return response()->json([
        'success' => true,
        'data' => DB::connection('oracle')->select("
            SELECT v.*, p.nom_produit 
            FROM VARIETE v 
            JOIN PRODUIT p ON v.id_produit = p.id_produit 
            ORDER BY v.nom_variete
        ")
    ]);
});

// ==================== STOCKS ====================
Route::get('stocks', function () {
    return response()->json([
        'success' => true,
        'data' => DB::connection('oracle')->select("
            SELECT s.*, v.nom_variete, p.nom_produit, r.date_recolte
            FROM STOCK s
            JOIN RECOLTE r ON s.id_recolte = r.id_recolte
            JOIN VARIETE v ON r.id_variete = v.id_variete
            JOIN PRODUIT p ON v.id_produit = p.id_produit
            WHERE s.quantite_restante > 0
            ORDER BY s.date_creation DESC
        ")
    ]);
});

// ==================== TEST ORACLE ====================
Route::get('test-oracle', function () {
    try {
        $tables = DB::connection('oracle')->select("SELECT table_name FROM user_tables");
        $views = DB::connection('oracle')->select("SELECT view_name FROM user_views");
        
        return response()->json([
            'success' => true,
            'database' => 'Oracle 21c XE',
            'tables_count' => count($tables),
            'views_count' => count($views),
            'tables' => array_column($tables, 'table_name'),
            'views' => array_column($views, 'view_name')
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});