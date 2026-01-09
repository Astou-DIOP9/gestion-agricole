@extends('layouts.app')

@section('title', 'Nouvelle Récolte')
@section('icon', 'fa-seedling')
@section('subtitle', 'Enregistrer une nouvelle récolte agricole')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Nouvelle Récolte</h2>
                <p class="text-gray-600 mt-2">Remplissez les informations pour enregistrer une récolte</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('recoltes.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('recoltes.store') }}" class="space-y-8">
            @csrf

            <!-- Section Informations de base -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-green-600"></i> Informations de base
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Date de récolte -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Date de récolte
                        </label>
                        <input type="date" 
                               name="date_recolte" 
                               value="{{ old('date_recolte', date('Y-m-d')) }}"
                               required
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                    </div>

                    <!-- Heure de récolte -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Heure de récolte
                        </label>
                        <input type="time" 
                               name="heure_recolte" 
                               value="{{ old('heure_recolte', date('H:i')) }}"
                               required
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                    </div>

                    <!-- Variété -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Variété
                        </label>
                        <select name="id_variete" 
                                required
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                            <option value="">Sélectionner une variété</option>
                            @foreach($varietes as $variete)
                            <option value="{{ $variete->id_variete }}" {{ old('id_variete') == $variete->id_variete ? 'selected' : '' }}>
                                {{ $variete->nom_variete }} ({{ $variete->nom_produit }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Qualité -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Qualité</label>
                        <select name="qualite" 
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                            <option value="">Sélectionner une qualité</option>
                            <option value="EXCELLENTE" {{ old('qualite') == 'EXCELLENTE' ? 'selected' : '' }}>Excellente</option>
                            <option value="BONNE" {{ old('qualite') == 'BONNE' ? 'selected' : '' }}>Bonne</option>
                            <option value="MOYENNE" {{ old('qualite') == 'MOYENNE' ? 'selected' : '' }}>Moyenne</option>
                            <option value="FAIBLE" {{ old('qualite') == 'FAIBLE' ? 'selected' : '' }}>Faible</option>
                        </select>
                    </div>

                    <!-- Quantité -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Quantité (kg)
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   name="quantite" 
                                   step="0.01"
                                   min="0.1"
                                   value="{{ old('quantite') }}"
                                   required
                                   placeholder="Ex: 150.5"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                            <div class="absolute right-3 top-3 text-gray-500">kg</div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Entrez la quantité en kilogrammes avec 2 décimales maximum</p>
                    </div>
                </div>
            </div>

            <!-- Section Commentaire -->
            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl p-6 border border-blue-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-comment-dots text-blue-600"></i> Observations
                </h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Commentaire</label>
                    <textarea name="commentaire" 
                              rows="4"
                              placeholder="Notes sur la récolte, conditions météo, observations particulières..."
                              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">{{ old('commentaire') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Optionnel - Maximum 500 caractères</p>
                </div>
            </div>

            <!-- Aperçu des dernières récoltes -->
            @if(isset($dernieresRecoltes) && count($dernieresRecoltes) > 0)
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-history text-gray-600"></i> Dernières récoltes
                </h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Variété</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Quantité</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Qualité</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($dernieresRecoltes as $recolte)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-sm">
                                    {{ \Carbon\Carbon::parse($recolte->date_recolte)->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <p class="font-medium">{{ $recolte->nom_variete }}</p>
                                    <p class="text-xs text-gray-600">{{ $recolte->nom_produit }}</p>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="font-medium text-green-600">
                                        {{ number_format($recolte->quantite, 1, ',', ' ') }} kg
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @php
                                        $qualiteColor = match($recolte->qualite) {
                                            'EXCELLENTE' => 'green',
                                            'BONNE' => 'blue',
                                            'MOYENNE' => 'yellow',
                                            'FAIBLE' => 'red',
                                            default => 'gray'
                                        };
                                    @endphp
                                    <span class="px-2 py-1 text-xs rounded-full bg-{{ $qualiteColor }}-100 text-{{ $qualiteColor }}-800">
                                        {{ $recolte->qualite ?? 'Non spécifié' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('recoltes.index') }}" 
                   class="px-6 py-3 border border-gray-300 rounded-xl font-medium text-gray-700 hover:bg-gray-50 transition flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" 
                        class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-8 py-3 rounded-xl font-medium transition-all duration-200 transform hover:-translate-y-1 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                    <i class="fas fa-check"></i> Enregistrer la récolte
                </button>
            </div>
        </form>
    </div>

    <!-- Informations utiles -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-6">
            <div class="flex items-start gap-3">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-lightbulb text-blue-600"></i>
                </div>
                <div>
                    <h4 class="font-medium text-gray-800 mb-2">Conseil</h4>
                    <p class="text-sm text-gray-600">Enregistrez vos récoltes rapidement pour un suivi optimal des stocks.</p>
                </div>
            </div>
        </div>

        <div class="bg-green-50 border border-green-100 rounded-xl p-6">
            <div class="flex items-start gap-3">
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="fas fa-chart-line text-green-600"></i>
                </div>
                <div>
                    <h4 class="font-medium text-gray-800 mb-2">Statistiques</h4>
                    <p class="text-sm text-gray-600">Les données alimentent automatiquement vos tableaux de bord.</p>
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
                    <p class="text-sm text-gray-600">Vérifiez les informations avant validation. Les données sont définitives.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    input:focus, select:focus, textarea:focus {
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }
</style>
@endpush
@endsection