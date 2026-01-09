@extends('layouts.app')

@section('title', 'Nouveau Produit')
@section('icon', 'fa-plus-circle')
@section('subtitle', 'Ajouter un nouveau produit agricole')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Nouveau Produit</h2>
                <p class="text-gray-600 mt-2">Ajoutez un nouveau produit à votre catalogue</p>
            </div>
            <a href="{{ route('produits.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>

        <form method="POST" action="{{ route('produits.store') }}" class="space-y-8">
            @csrf

            <!-- Informations du produit -->
            <div class="space-y-6">
                <!-- Nom du produit -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-red-500">*</span> Nom du produit
                    </label>
                    <input type="text" 
                           name="nom_produit" 
                           value="{{ old('nom_produit') }}"
                           required
                           maxlength="100"
                           placeholder="Ex: Tomate, Carotte, Chou..."
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 text-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                    <p class="text-xs text-gray-500 mt-1">Nom complet du produit agricole</p>
                </div>

                <!-- Catégorie -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
                    <select name="categorie" 
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                        <option value="">Sélectionner une catégorie</option>
                        <option value="LEGUME" {{ old('categorie') == 'LEGUME' ? 'selected' : '' }}>Légume</option>
                        <option value="FRUIT" {{ old('categorie') == 'FRUIT' ? 'selected' : '' }}>Fruit</option>
                        <option value="RACINE" {{ old('categorie') == 'RACINE' ? 'selected' : '' }}>Racine</option>
                        <option value="FEUILLE" {{ old('categorie') == 'FEUILLE' ? 'selected' : '' }}>Feuille</option>
                        <option value="GRAINE" {{ old('categorie') == 'GRAINE' ? 'selected' : '' }}>Graine</option>
                        <option value="AUTRE" {{ old('categorie') == 'AUTRE' ? 'selected' : '' }}>Autre</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Optionnel - Permet de classer vos produits</p>
                </div>

                <!-- Aperçu -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-eye text-green-600"></i> Aperçu
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Nom:</span>
                            <span id="previewNom" class="font-medium text-gray-800">-</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Catégorie:</span>
                            <span id="previewCategorie" class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-full">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('produits.index') }}" 
                   class="px-6 py-3 border border-gray-300 rounded-xl font-medium text-gray-700 hover:bg-gray-50 transition flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" 
                        class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-8 py-3 rounded-xl font-medium transition-all duration-200 transform hover:-translate-y-1 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                    <i class="fas fa-check"></i> Créer le produit
                </button>
            </div>
        </form>
    </div>

    <!-- Informations utiles -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-6">
            <div class="flex items-start gap-3">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-lightbulb text-blue-600"></i>
                </div>
                <div>
                    <h4 class="font-medium text-gray-800 mb-2">Conseil</h4>
                    <p class="text-sm text-gray-600">Utilisez des noms simples et reconnaissables pour vos produits.</p>
                </div>
            </div>
        </div>

        <div class="bg-amber-50 border border-amber-100 rounded-xl p-6">
            <div class="flex items-start gap-3">
                <div class="p-3 bg-amber-100 rounded-lg">
                    <i class="fas fa-exclamation-triangle text-amber-600"></i>
                </div>
                <div>
                    <h4 class="font-medium text-gray-800 mb-2">Important</h4>
                    <p class="text-sm text-gray-600">Un produit ne peut être supprimé que s'il n'a pas de variétés associées.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Mise à jour de l'aperçu en temps réel
    document.addEventListener('DOMContentLoaded', function() {
        const nomInput = document.querySelector('input[name="nom_produit"]');
        const categorieSelect = document.querySelector('select[name="categorie"]');
        
        function updatePreview() {
            // Nom
            document.getElementById('previewNom').textContent = 
                nomInput.value || '-';
            
            // Catégorie
            const categorie = categorieSelect.value;
            const previewCategorie = document.getElementById('previewCategorie');
            previewCategorie.textContent = categorie || '-';
            
            // Style de la catégorie
            if (categorie) {
                const colors = {
                    'LEGUME': 'bg-green-100 text-green-700',
                    'FRUIT': 'bg-red-100 text-red-700',
                    'RACINE': 'bg-amber-100 text-amber-700',
                    'FEUILLE': 'bg-blue-100 text-blue-700',
                    'GRAINE': 'bg-purple-100 text-purple-700',
                    'AUTRE': 'bg-gray-100 text-gray-700'
                };
                previewCategorie.className = 'px-3 py-1 text-xs rounded-full ' + (colors[categorie] || 'bg-gray-100 text-gray-700');
            } else {
                previewCategorie.className = 'px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-full';
            }
        }
        
        nomInput.addEventListener('input', updatePreview);
        categorieSelect.addEventListener('change', updatePreview);
        
        // Initialiser l'aperçu
        updatePreview();
    });
</script>
@endpush
@endsection