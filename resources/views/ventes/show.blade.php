@extends('layouts.app')

@section('title', 'Vente #' . $vente->id_vente)
@section('icon', 'fa-receipt')
@section('subtitle', 'Détails de la vente')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <!-- En-tête -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Vente #{{ $vente->id_vente }}</h2>
                <p class="text-gray-600 mt-2">Détails de la vente enregistrée</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('ventes.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>

        <!-- Informations de la vente -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Colonne gauche -->
            <div class="space-y-6">
                <!-- Produit -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-seedling text-green-600"></i> Produit vendu
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Variété:</span>
                            <span class="font-medium text-gray-800">{{ $vente->nom_variete }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Produit:</span>
                            <span class="font-medium text-gray-800">{{ $vente->nom_produit }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Date récolte:</span>
                            <span class="font-medium text-gray-800">
                                {{ \Carbon\Carbon::parse($vente->date_recolte)->format('d/m/Y') }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Quantité récoltée:</span>
                            <span class="font-medium text-green-600">
                                {{ number_format($vente->quantite_recolte, 2, ',', ' ') }} kg
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Client -->
                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl p-6 border border-blue-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-user text-blue-600"></i> Informations client
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Type de client:</span>
                            <span class="px-3 py-1 text-sm rounded-full 
                                @if($vente->client_type == 'REVENDEUR') bg-blue-100 text-blue-800
                                @elseif($vente->client_type == 'RESTAURANT') bg-green-100 text-green-800
                                @elseif($vente->client_type == 'PARTICULIER') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $vente->client_type }}
                            </span>
                        </div>
                        @if(!empty($vente->commentaire ?? null))
                        <div>
                            <span class="text-gray-600 block mb-2">Commentaire:</span>
                            <p class="text-gray-800 bg-white p-3 rounded-lg border">
                                {{ $vente->commentaire }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Colonne droite -->
            <div class="space-y-6">
                <!-- Détails financiers -->
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl p-6 border border-amber-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-calculator text-amber-600"></i> Détails financiers
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Quantité vendue:</span>
                            <span class="font-medium text-gray-800">
                                {{ number_format($vente->quantite_vendue, 2, ',', ' ') }} kg
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Prix unitaire:</span>
                            <span class="font-medium text-blue-600">
                                {{ number_format($vente->prix_unitaire, 2, ',', ' ') }} €/kg
                            </span>
                        </div>
                        <div class="border-t border-amber-200 pt-3">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-gray-800">Montant total:</span>
                                <span class="text-2xl font-bold text-purple-600">
                                    {{ number_format($vente->montant_total, 2, ',', ' ') }} €
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">
                                {{ number_format($vente->quantite_vendue, 2, ',', ' ') }} kg × 
                                {{ number_format($vente->prix_unitaire, 2, ',', ' ') }} €/kg
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Informations de la vente -->
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-6 border border-purple-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-purple-600"></i> Informations vente
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Date et heure:</span>
                            <span class="font-medium text-gray-800">
                                {{ \Carbon\Carbon::parse($vente->date_vente)->format('d/m/Y H:i') }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Statut:</span>
                            <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-800">
                                Terminée
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Enregistrée le:</span>
                            <span class="font-medium text-gray-800">
                                {{ \Carbon\Carbon::parse($vente->date_vente)->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 mt-6 border-t border-gray-200">
            <a href="{{ route('ventes.create') }}?recolte={{ $vente->id_recolte }}" 
               class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-8 py-3 rounded-xl font-medium transition flex items-center justify-center gap-2">
                <i class="fas fa-plus"></i> Nouvelle vente
            </a>
        </div>
    </div>

    <!-- Récolte associée -->
    <div class="mt-8 bg-white rounded-2xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-history text-blue-600"></i> Récolte associée
        </h3>
        
        <div class="bg-gray-50 rounded-xl p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-3 bg-white rounded-lg">
                    <p class="text-xs text-gray-600 mb-1">Variété</p>
                    <p class="font-medium text-gray-800">{{ $vente->nom_variete }}</p>
                </div>
                <div class="text-center p-3 bg-white rounded-lg">
                    <p class="text-xs text-gray-600 mb-1">Date récolte</p>
                    <p class="font-medium text-gray-800">
                        {{ \Carbon\Carbon::parse($vente->date_recolte)->format('d/m/Y') }}
                    </p>
                </div>
                <div class="text-center p-3 bg-white rounded-lg">
                    <p class="text-xs text-gray-600 mb-1">Quantité récoltée</p>
                    <p class="font-medium text-green-600">
                        {{ number_format($vente->quantite_recolte, 2, ',', ' ') }} kg
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection