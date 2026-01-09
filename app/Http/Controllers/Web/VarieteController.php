<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Variete;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VarieteController extends Controller
{
    public function index(Request $request)
    {
        $produitId = $request->input('produit');

        $query = Variete::with('produit');

        if ($produitId) {
            $query->where('id_produit', $produitId);
        }

        $varietes = $query->orderBy('nom_variete')->get();
        $produits = Produit::orderBy('nom_produit')->get();

        return view('varietes.index', compact('varietes', 'produits', 'produitId'));
    }

    public function create()
    {
        $produits = Produit::orderBy('nom_produit')->get();
        return view('varietes.create', compact('produits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_variete' => 'required|string|max:100',
            'description' => 'nullable|string',
            'periode_optimale' => 'nullable|string|max:50',
            'id_produit' => 'required|exists:PRODUIT,id_produit'
        ]);

        Variete::create($request->only(['nom_variete', 'description', 'periode_optimale', 'id_produit']));

        return redirect()->route('varietes.index')
            ->with('success', 'Variété créée avec succès !');
    }

    public function show(Variete $variete)
    {
        // Statistiques pour cette variété
        $stats = DB::connection('oracle')->selectOne("
            SELECT 
                COUNT(*) as total_recoltes,
                SUM(r.quantite) as total_quantite,
                AVG(r.quantite) as moyenne_quantite,
                MIN(r.date_recolte) as premiere_recolte,
                MAX(r.date_recolte) as derniere_recolte
            FROM RECOLTE r
            WHERE r.id_variete = ?
        ", [$variete->id_variete]);

        // Dernières récoltes
        $recoltes = DB::connection('oracle')->select("
            SELECT * FROM RECOLTE
            WHERE id_variete = ?
            ORDER BY date_recolte DESC
            FETCH FIRST 10 ROWS ONLY
        ", [$variete->id_variete]);

        // Prix du marché
        $prixMarche = DB::connection('oracle')->select("
            SELECT * FROM PRIX_MARCHE
            WHERE id_variete = ?
            ORDER BY date_prix DESC
            FETCH FIRST 5 ROWS ONLY
        ", [$variete->id_variete]);

        return view('varietes.show', compact('variete', 'stats', 'recoltes', 'prixMarche'));
    }

    public function edit(Variete $variete)
    {
        $produits = Produit::orderBy('nom_produit')->get();
        return view('varietes.edit', compact('variete', 'produits'));
    }

    public function update(Request $request, Variete $variete)
    {
        $request->validate([
            'nom_variete' => 'required|string|max:100',
            'description' => 'nullable|string',
            'periode_optimale' => 'nullable|string|max:50',
            'id_produit' => 'required|exists:PRODUIT,id_produit'
        ]);

        $variete->update($request->only(['nom_variete', 'description', 'periode_optimale', 'id_produit']));

        return redirect()->route('varietes.index')
            ->with('success', 'Variété mise à jour avec succès !');
    }

    public function destroy(Variete $variete)
    {
        // Vérifier s'il y a des récoltes associées
        $recoltes_count = DB::connection('oracle')->selectOne("
            SELECT COUNT(*) as count
            FROM RECOLTE
            WHERE id_variete = ?
        ", [$variete->id_variete])->count;

        if ($recoltes_count > 0) {
            return redirect()->route('varietes.index')
                ->with('error', 'Impossible de supprimer cette variété car elle a des récoltes associées');
        }

        $variete->delete();

        return redirect()->route('varietes.index')
            ->with('success', 'Variété supprimée avec succès !');
    }
}
