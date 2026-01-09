<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\RecolteController;
use App\Http\Controllers\Web\VenteController;
use App\Http\Controllers\Web\StockController;
use App\Http\Controllers\Web\ProduitController;
use App\Http\Controllers\Web\VarieteController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Page d'accueil / Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/filter', [DashboardController::class, 'filter'])->name('dashboard.filter');

// Test Oracle connection


Route::get('/test-oracle', function () {
    $user = DB::connection('oracle')->selectOne("SELECT USER FROM DUAL");
    $tables = DB::connection('oracle')->select("SELECT table_name FROM user_tables WHERE ROWNUM <= 5");
    return [
        'current_user' => $user->user ?? 'Unknown',
        'tables_sample' => $tables,
        'connection' => 'oracle'
    ];
});

// Gestion des récoltes
Route::resource('recoltes', RecolteController::class);
Route::get('recoltes/{id}/quantite', [RecolteController::class, 'quantiteDisponible'])->name('recoltes.quantite');
Route::get('recoltes/statistiques/mensuelles', [RecolteController::class, 'statistiquesMensuelles'])->name('recoltes.statistiques');

// Gestion des ventes
Route::resource('ventes', VenteController::class);
Route::get('ventes/chiffre-affaires', [VenteController::class, 'chiffreAffaires'])->name('ventes.chiffre-affaires');
Route::get('ventes/top', [VenteController::class, 'topVentes'])->name('ventes.top');
// Assurez-vous que vous avez ces routes
Route::get('/ventes', [VenteController::class, 'index'])->name('ventes.index');
Route::get('/ventes/create', [VenteController::class, 'create'])->name('ventes.create');
Route::post('/ventes', [VenteController::class, 'store'])->name('ventes.store');
Route::get('/ventes/{id}', [VenteController::class, 'show'])->name('ventes.show');
// Gestion des stocks
Route::get('stocks', [StockController::class, 'index'])->name('stocks.index');
Route::get('stocks/alertes', [StockController::class, 'alertes'])->name('stocks.alertes');

// Gestion des produits et variétés
Route::resource('produits', ProduitController::class);
Route::resource('varietes', VarieteController::class);

// Données pour API (optionnel - pour garder notre API existante)


// Routes supplémentaires pour le dashboard
Route::get('/dashboard/alertes', [DashboardController::class, 'alertes'])->name('dashboard.alertes');
Route::get('/dashboard/statistiques-recoltes', [DashboardController::class, 'statistiquesRecoltes'])->name('dashboard.statistiques-recoltes');

// Routes pour les stocks
Route::get('/stocks/alertes', [StockController::class, 'alertes'])->name('stocks.alertes');
Route::get('/stocks/export', [StockController::class, 'export'])->name('stocks.export');

// Routes pour les statistiques
Route::get('/statistiques/ventes-top', [VenteController::class, 'topVentes'])->name('ventes.top');
Route::get('/statistiques/recoltes-mensuelles', [RecolteController::class, 'statistiquesMensuelles'])->name('recoltes.statistiques');
Route::get('/statistiques/chiffre-affaires', [VenteController::class, 'chiffreAffaires'])->name('ventes.chiffre-affaires');

// Routes pour l'export
Route::get('/export/recoltes', [RecolteController::class, 'export'])->name('recoltes.export');
Route::get('/export/ventes', [VenteController::class, 'export'])->name('ventes.export');
