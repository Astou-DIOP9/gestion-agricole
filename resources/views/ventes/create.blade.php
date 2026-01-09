@extends('layouts.app')

@section('title', 'Nouvelle Vente')
@section('icon', 'fa-cash-register')
@section('subtitle', 'Enregistrer une nouvelle vente agricole')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Nouvelle Vente</h2>
                <p class="text-gray-600 mt-2">Enregistrez une vente de produits agricoles</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('ventes.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('ventes.store') }}" id="venteForm" class="space-y-8">
            @csrf

            <!-- Sélection de la récolte -->
            <div class="bg-linear-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-seedling text-green-600"></i> Sélection de la récolte
                </h3>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-red-500">*</span> Récolte disponible
                    </label>
                    <select name="id_recolte" 
                            id="id_recolte" 
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                            onchange="updateRecolteInfo(this.value)">
                        <option value="">Choisir une récolte disponible</option>
                        @foreach($recoltesDisponibles as $recolte)
                        <option value="{{ $recolte->id_recolte }}" 
                                data-quantite="{{ $recolte->quantite_disponible }}"
                                data-prix="{{ $recolte->prix_marche ?? 0 }}"
                                data-variete="{{ $recolte->nom_variete }}"
                                data-produit="{{ $recolte->nom_produit }}"
                                {{ $recolteSelected && $recolteSelected->id_recolte == $recolte->id_recolte ? 'selected' : '' }}>
                            #{{ $recolte->id_recolte }} - {{ $recolte->nom_variete }} ({{ $recolte->nom_produit }})
                            - Disponible: {{ number_format($recolte->quantite_disponible, 4, ',', ' ') }} kg
                            - Récolté le: {{ \Carbon\Carbon::parse($recolte->date_recolte)->format('d/m/Y') }}
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Sélectionnez la récolte que vous souhaitez vendre</p>
                </div>

                <!-- Info récolte sélectionnée -->
                <div id="recolteInfo" class="hidden bg-white rounded-lg p-4 border border-green-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="text-center p-3 bg-green-50 rounded-lg">
                            <p class="text-xs text-gray-600 mb-1">Variété</p>
                            <p id="infoVariete" class="font-medium text-gray-800">-</p>
                        </div>
                        <div class="text-center p-3 bg-blue-50 rounded-lg">
                            <p class="text-xs text-gray-600 mb-1">Produit</p>
                            <p id="infoProduit" class="font-medium text-gray-800">-</p>
                        </div>
                        <div class="text-center p-3 bg-amber-50 rounded-lg">
                            <p class="text-xs text-gray-600 mb-1">Disponible</p>
                            <p id="infoDisponible" class="font-bold text-green-600">0 kg</p>
                        </div>
                        <div class="text-center p-3 bg-purple-50 rounded-lg">
                            <p class="text-xs text-gray-600 mb-1">Prix suggéré</p>
                            <p id="infoPrix" class="font-bold text-blue-600">0 FCFA/kg</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations de la vente -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Colonne gauche -->
                <div class="space-y-6">
                    <!-- Date de vente -->
                    <div class="bg-linear-to-r from-blue-50 to-cyan-50 rounded-xl p-6 border border-blue-100">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="far fa-calendar-alt text-blue-600"></i> Date et client
                        </h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="text-red-500">*</span> Date de vente
                                </label>
                                <input type="datetime-local" 
                                       name="date_vente" 
                                       id="date_vente"
                                       value="{{ old('date_vente', date('Y-m-d\TH:i')) }}"
                                       required
                                       onchange="formatDateForOracle(this)"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                                
                                <!-- CHAMP CACHÉ POUR ORACLE -->
                                <input type="hidden" name="date_vente_oracle" id="date_vente_oracle" 
                                       value="{{ old('date_vente_oracle', date('Y-m-d H:i:s')) }}">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="text-red-500">*</span> Type de client
                                </label>
                                <select name="client_type" 
                                        required
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                                    <option value="">Type de client</option>
                                    <option value="PARTICULIER" {{ old('client_type') == 'PARTICULIER' ? 'selected' : '' }}>Particulier</option>
                                    <option value="REVENDEUR" {{ old('client_type') == 'REVENDEUR' ? 'selected' : '' }}>Revendeur</option>
                                    <option value="RESTAURANT" {{ old('client_type') == 'RESTAURANT' ? 'selected' : '' }}>Restaurant</option>
                                    <option value="AUTRE" {{ old('client_type') == 'AUTRE' ? 'selected' : '' }}>Autre</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Commentaire -->
                    <div class="bg-linear-to-r from-gray-50 to-gray-100 rounded-xl p-6 border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-comment text-gray-600"></i> Informations supplémentaires
                        </h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Commentaire (optionnel)</label>
                            <textarea name="commentaire" 
                                      rows="3"
                                      placeholder="Notes sur la vente, conditions particulières..."
                                      class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">{{ old('commentaire') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Colonne droite -->
                <div class="space-y-6">
                    <!-- Quantité et prix -->
                    <div class="bg-linear-to-r from-amber-50 to-orange-50 rounded-xl p-6 border border-amber-100">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-calculator text-amber-600"></i> Détails financiers
                        </h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="text-red-500">*</span> Quantité à vendre (kg)
                                </label>
                                <input type="number" 
                                       name="quantite_vendue" 
                                       id="quantite_vendue"
                                       step="0.01"
                                       min="0.1"
                                       value="{{ old('quantite_vendue') }}"
                                       required
                                       oninput="calculateTotal()"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                                <p class="text-xs text-gray-500 mt-1">
                                    Quantité maximale disponible: <span id="maxQuantite" class="font-medium">0</span> kg
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="text-red-500">*</span> Prix unitaire (FCFA/kg)
                                </label>
                                <input type="number" 
                                       name="prix_unitaire" 
                                       id="prix_unitaire"
                                       step="0.01"
                                       min="0.01"
                                       value="{{ old('prix_unitaire') }}"
                                       required
                                       oninput="calculateTotal()"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                                <p class="text-xs text-gray-500 mt-1">
                                    Prix suggéré: <span id="suggestedPrice" class="font-medium">0</span> FCFA/kg
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Résumé de la vente -->
                    <div class="bg-linear-to-r from-purple-50 to-pink-50 rounded-xl p-6 border border-purple-100">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-receipt text-purple-600"></i> Résumé de la vente
                        </h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Quantité</span>
                                <span id="resumeQuantite" class="font-medium">0 kg</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Prix unitaire</span>
                                <span id="resumePrix" class="font-medium">0 FCFA/kg</span>
                            </div>
                            <div class="border-t border-gray-200 pt-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-800">Montant total</span>
                                    <span id="montantTotal" class="text-2xl font-bold text-purple-600">0 FCFA </span>
                                </div>
                                <p id="calculDetail" class="text-xs text-gray-500 mt-1 text-right">0 kg × 0 FCFA/kg</p>
                            </div>
                        </div>
                        <input type="hidden" name="montant_total" id="montant_total_input" value="0">
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('ventes.index') }}" 
                   class="px-6 py-3 border border-gray-300 rounded-xl font-medium text-gray-700 hover:bg-gray-50 transition flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-8 py-3 rounded-xl font-medium transition-all duration-200 transform hover:-translate-y-1 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                    <i class="fas fa-check"></i> Enregistrer la vente
                </button>
            </div>
        </form>
    </div>

    <!-- Liste des récoltes disponibles -->
    @if(count($recoltesDisponibles) > 0)
    <div class="mt-8 bg-white rounded-2xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center gap-2">
            <i class="fas fa-boxes text-green-600"></i> Récoltes disponibles
        </h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Variété</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Date récolte</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Disponible</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Prix marché</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recoltesDisponibles as $recolte)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-sm font-medium text-gray-800">#{{ $recolte->id_recolte }}</td>
                        <td class="px-4 py-3 text-sm">
                            <p class="font-medium">{{ $recolte->nom_variete }}</p>
                            <p class="text-xs text-gray-600">{{ $recolte->nom_produit }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            {{ \Carbon\Carbon::parse($recolte->date_recolte)->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span class="font-medium text-green-600">
                                {{ number_format($recolte->quantite_disponible, 2, ',', ' ') }} kg
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            {{ number_format($recolte->prix_marche ?? 0, 2, ',', ' ') }} FCFA/kg
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <button onclick="selectRecolte({{ $recolte->id_recolte }})"
                                    class="text-green-600 hover:text-green-700 font-medium text-sm">
                                Sélectionner
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    function formatDateForOracle(input) {
        // Convertit YYYY-MM-DDTHH:mm en YYYY-MM-DD HH24:MI:SS pour Oracle
        const dateValue = input.value.replace('T', ' ') + ':00';
        document.getElementById('date_vente_oracle').value = dateValue;
    }

    // Initialiser au chargement
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('date_vente');
        if (dateInput.value) {
            formatDateForOracle(dateInput);
        }
    });

    function updateRecolteInfo(recolteId) {
        const select = document.getElementById('id_recolte');
        const selectedOption = select.options[select.selectedIndex];
        const infoDiv = document.getElementById('recolteInfo');
        
        if (selectedOption && selectedOption.value) {
            // Afficher les infos
            infoDiv.classList.remove('hidden');
            
            // Mettre à jour les informations
            document.getElementById('infoVariete').textContent = selectedOption.getAttribute('data-variete') || '-';
            document.getElementById('infoProduit').textContent = selectedOption.getAttribute('data-produit') || '-';
            
            const maxQuantite = parseFloat(selectedOption.getAttribute('data-quantite')) || 0;
            document.getElementById('infoDisponible').textContent = maxQuantite.toFixed(2) + ' kg';
            document.getElementById('maxQuantite').textContent = maxQuantite.toFixed(2);
            
            const suggestedPrice = parseFloat(selectedOption.getAttribute('data-prix')) || 0;
            document.getElementById('infoPrix').textContent = suggestedPrice.toFixed(2) + ' FCFA/kg';
            document.getElementById('suggestedPrice').textContent = suggestedPrice.toFixed(2);
            
            // Mettre à jour le prix unitaire
            document.getElementById('prix_unitaire').value = suggestedPrice.toFixed(2);
            
            // Recalculer le total
            calculateTotal();
        } else {
            infoDiv.classList.add('hidden');
        }
    }

    function calculateTotal() {
        const quantite = parseFloat(document.getElementById('quantite_vendue').value) || 0;
        const prix = parseFloat(document.getElementById('prix_unitaire').value) || 0;
        const total = quantite * prix;
        
        // Mettre à jour l'affichage
        document.getElementById('montantTotal').textContent = total.toFixed(2) + ' FCFA';
        document.getElementById('resumeQuantite').textContent = quantite.toFixed(2) + ' kg';
        document.getElementById('resumePrix').textContent = prix.toFixed(2) + ' FCFA/kg';
        document.getElementById('calculDetail').textContent = 
            quantite.toFixed(2) + ' kg × ' + prix.toFixed(2) + ' FCFA/kg';
        document.getElementById('montant_total_input').value = total.toFixed(2);
        
        // Vérifier la quantité disponible
        const maxQuantite = parseFloat(document.getElementById('maxQuantite').textContent) || 0;
        if (quantite > maxQuantite) {
            document.getElementById('quantite_vendue').classList.add('border-red-500');
        } else {
            document.getElementById('quantite_vendue').classList.remove('border-red-500');
        }
    }

    function selectRecolte(recolteId) {
        const select = document.getElementById('id_recolte');
        select.value = recolteId;
        updateRecolteInfo(recolteId);
        
        // Scroll vers le formulaire
        document.getElementById('recolteInfo').scrollIntoView({ behavior: 'smooth' });
    }

    // Initialiser si une récolte est présélectionnée
    @if($recolteSelected)
    document.addEventListener('DOMContentLoaded', function() {
        updateRecolteInfo('{{ $recolteSelected->id_recolte }}');
        calculateTotal();
        
        // Initialiser la date Oracle
        const dateInput = document.getElementById('date_vente');
        if (dateInput.value) {
            formatDateForOracle(dateInput);
        }
    });
    @endif
</script>
@endpush
@endsection