@extends('layouts.app')

@section('title', 'Nouvelle Variété')
@section('icon', 'fa-plus-circle')
@section('subtitle', 'Ajouter une nouvelle variété agricole')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Nouvelle Variété</h2>
                <p class="text-gray-600 mt-2">Ajoutez une nouvelle variété à votre catalogue</p>
            </div>
            <a href="{{ route('varietes.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>

        <form method="POST" action="{{ route('varietes.store') }}" class="space-y-8">
            @csrf

            <!-- Informations de la variété -->
            <div class="space-y-6">
                <!-- Produit parent -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-red-500">*</span> Produit
                    </label>
                    <select name="id_produit" 
                            required
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                        <option value="">Sélectionner un produit</option>
                        @foreach($produits as $produit)
                        <option value="{{ $produit->id_produit }}" 
                                {{ (request('produit') == $produit->id_produit || old('id_produit') == $produit->id_produit) ? 'selected' : '' }}>
                            {{ $produit->nom_produit }}
                            @if($produit->categorie)
                            ({{ $produit->categorie }})
                            @endif
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Produit agricole auquel appartient cette variété</p>
                </div>

                <!-- Nom de la variété -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-red-500">*</span> Nom de la variété
                    </label>
                    <input type="text" 
                           name="nom_variete" 
                           value="{{ old('nom_variete') }}"
                           required
                           maxlength="100"
                           placeholder="Ex: Roma, Carotte Nantaise, Gala..."
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 text-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                    <p class="text-xs text-gray-500 mt-1">Nom spécifique de la variété</p>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" 
                              rows="4"
                              placeholder="Caractéristiques particulières de cette variété..."
                              class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">{{ old('description') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Optionnel - Description détaillée de la variété</p>
                </div>

                <!-- Période optimale -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Période optimale</label>
                    <input type="text" 
                           name="periode_optimale" 
                           value="{{ old('periode_optimale') }}"
                           maxlength="50"
                           placeholder="Ex: Printemps, Été, Automne..."
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                    <p class="text-xs text-gray-500 mt-1">Période de culture optimale (optionnel)</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('varietes.index') }}" 
                   class="px-6 py-3 border border-gray-300 rounded-xl font-medium text-gray-700 hover:bg-gray-50 transition flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" 
                        class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-8 py-3 rounded-xl font-medium transition-all duration-200 transform hover:-translate-y-1 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                    <i class="fas fa-check"></i> Créer la variété
                </button>
            </div>
        </form>
    </div>

    <!-- Aide -->
    <div class="mt-8 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-2xl p-6 border border-blue-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-question-circle text-blue-600"></i> À propos des variétés
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <p class="font-medium text-gray-800">Exemples de variétés :</p>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>• Tomate Roma</li>
                    <li>• Carotte Nantaise</li>
                    <li>• Pomme Gala</li>
                    <li>• Salade Batavia</li>
                </ul>
            </div>
            <div class="space-y-2">
                <p class="font-medium text-gray-800">Conseils :</p>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>• Utilisez des noms reconnus</li>
                    <li>• Précisez la période optimale</li>
                    <li>• Décrivez les caractéristiques</li>
                    <li>• Associez à un produit existant</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection