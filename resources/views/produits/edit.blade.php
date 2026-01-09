@extends('layouts.app')

@section('title', 'Modifier ' . $produit->nom_produit)
@section('icon', 'fa-edit')
@section('subtitle', 'Modifier les informations du produit')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Modifier le produit</h2>
                <p class="text-gray-600 mt-2">Mettez à jour les informations de {{ $produit->nom_produit }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('produits.show', $produit) }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center gap-2">
                    <i class="fas fa-eye"></i> Voir
                </a>
                <a href="{{ route('produits.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('produits.update', $produit) }}" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Informations du produit -->
            <div class="space-y-6">
                <!-- Nom du produit -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-red-500">*</span> Nom du produit
                    </label>
                    <input type="text" 
                           name="nom_produit" 
                           value="{{ old('nom_produit', $produit->nom_produit) }}"
                           required
                           maxlength="100"
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 text-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                    <p class="text-xs text-gray-500 mt-1">Nom complet du produit agricole</p>
                </div>

                <!-- Catégorie -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
                    <select name="categorie" 
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                        <option value="">Sélectionner une catégorie</option>
                        <option value="LEGUME" {{ old('categorie', $produit->categorie) == 'LEGUME' ? 'selected' : '' }}>Légume</option>
                        <option value="FRUIT" {{ old('categorie', $produit->categorie) == 'FRUIT' ? 'selected' : '' }}>Fruit</option>
                        <option value="RACINE" {{ old('categorie', $produit->categorie) == 'RACINE' ? 'selected' : '' }}>Racine</option>
                        <option value="FEUILLE" {{ old('categorie', $produit->categorie) == 'FEUILLE' ? 'selected' : '' }}>Feuille</option>
                        <option value="GRAINE" {{ old('categorie', $produit->categorie) == 'GRAINE' ? 'selected' : '' }}>Graine</option>
                        <option value="AUTRE" {{ old('categorie', $produit->categorie) == 'AUTRE' ? 'selected' : '' }}>Autre</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Optionnel - Permet de classer vos produits</p>
                </div>

                <!-- Informations actuelles -->
                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl p-6 border border-blue-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600"></i> Informations actuelles
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Variétés associées</p>
                            <p class="text-lg font-bold text-gray-800">
                                {{ \DB::connection('oracle')->selectOne("SELECT COUNT(*) as count FROM VARIETE WHERE id_produit = ?", [$produit->id_produit])->count ?? 0 }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Récoltes totales</p>
                            <p class="text-lg font-bold text-gray-800">
                                {{ \DB::connection('oracle')->selectOne("SELECT COUNT(*) as count FROM RECOLTE r JOIN VARIETE v ON r.id_variete = v.id_variete WHERE v.id_produit = ?", [$produit->id_produit])->count ?? 0 }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('produits.show', $produit) }}" 
                   class="px-6 py-3 border border-gray-300 rounded-xl font-medium text-gray-700 hover:bg-gray-50 transition flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" 
                        class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-8 py-3 rounded-xl font-medium transition-all duration-200 transform hover:-translate-y-1 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Mettre à jour
                </button>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    @if(\DB::connection('oracle')->selectOne("SELECT COUNT(*) as count FROM VARIETE WHERE id_produit = ?", [$produit->id_produit])->count == 0)
    <div class="mt-8 bg-gradient-to-r from-red-50 to-rose-50 rounded-2xl p-6 border border-red-100">
        <h3 class="text-lg font-semibold text-red-800 mb-4 flex items-center gap-2">
            <i class="fas fa-exclamation-triangle text-red-600"></i> Zone de danger
        </h3>
        
        <div class="space-y-4">
            <p class="text-red-700">
                Cette action est irréversible. Une fois supprimé, le produit ne pourra plus être récupéré.
            </p>
            
            <form action="{{ route('produits.destroy', $produit) }}" 
                  method="POST" 
                  onsubmit="return confirm('Êtes-vous ABSOLUMENT sûr de vouloir supprimer ce produit ? Cette action est irréversible.')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white px-6 py-3 rounded-xl font-medium transition flex items-center gap-2">
                    <i class="fas fa-trash"></i> Supprimer définitivement
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection