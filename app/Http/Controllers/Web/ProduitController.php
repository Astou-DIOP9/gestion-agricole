<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduitController extends Controller
{
    public function index()
    {
        $produits = Produit::orderBy('nom_produit')->get();

        // Statistiques pour chaque produit
        $produits->each(function ($produit) {
            $produit->varietes_count = \DB::connection('oracle')->selectOne("
                SELECT COUNT(*) as count
                FROM VARIETE
                WHERE id_produit = ?
            ", [$produit->id_produit])->count ?? 0;

            $produit->recoltes_count = \DB::connection('oracle')->selectOne("
                SELECT COUNT(*) as count
                FROM RECOLTE r
                JOIN VARIETE v ON r.id_variete = v.id_variete
                WHERE v.id_produit = ?
            ", [$produit->id_produit])->count ?? 0;
        });

        return view('produits.index', compact('produits'));
    }

    public function create()
    {
        return view('produits.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_produit' => 'required|string|max:100',
            'categorie' => 'nullable|string|max:50'
        ]);

        Produit::create($request->only(['nom_produit', 'categorie']));

        return redirect()->route('produits.index')
            ->with('success', 'Produit créé avec succès !');
    }

    public function show(Produit $produit)
    {
        // Récupérer les variétés de ce produit
        $varietes = \DB::connection('oracle')->select("
            SELECT * FROM VARIETE 
            WHERE id_produit = ?
            ORDER BY nom_variete
        ", [$produit->id_produit]);

        // Statistiques des récoltes
        $stats = \DB::connection('oracle')->selectOne("
            SELECT 
                COUNT(*) as total_recoltes,
                SUM(r.quantite) as total_quantite,
                AVG(r.quantite) as moyenne_quantite
            FROM RECOLTE r
            JOIN VARIETE v ON r.id_variete = v.id_variete
            WHERE v.id_produit = ?
        ", [$produit->id_produit]);

        return view('produits.show', compact('produit', 'varietes', 'stats'));
    }

    public function edit(Produit $produit)
    {
        return view('produits.edit', compact('produit'));
    }

    public function update(Request $request, Produit $produit)
    {
        $request->validate([
            'nom_produit' => 'required|string|max:100',
            'categorie' => 'nullable|string|max:50'
        ]);

        $produit->update($request->only(['nom_produit', 'categorie']));

        return redirect()->route('produits.index')
            ->with('success', 'Produit mis à jour avec succès !');
    }

    public function destroy(Produit $produit)
    {
        // Vérifier s'il y a des variétés associées
        $varietes_count = \DB::connection('oracle')->selectOne("
            SELECT COUNT(*) as count
            FROM VARIETE
            WHERE id_produit = ?
        ", [$produit->id_produit])->count;

        if ($varietes_count > 0) {
            return redirect()->route('produits.index')
                ->with('error', 'Impossible de supprimer ce produit car il a des variétés associées');
        }

        $produit->delete();

        return redirect()->route('produits.index')
            ->with('success', 'Produit supprimé avec succès !');
    }
}
