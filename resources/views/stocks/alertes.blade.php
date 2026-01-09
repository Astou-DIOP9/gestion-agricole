@extends('layouts.app')

@section('title', 'Alertes Stocks')
@section('icon', 'fa-exclamation-triangle')
@section('subtitle', 'Stocks nécessitant une attention urgente')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Alertes urgentes</p>
                    <p class="text-3xl font-bold">{{ count($alertes) }}</p>
                </div>
                <div class="p-3 bg-white/20 rounded-full">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Périmés</p>
                    <p class="text-3xl font-bold">
                        {{ collect($alertes)->where('etat', 'PERIME')->count() }}
                    </p>
                </div>
                <div class="p-3 bg-white/20 rounded-full">
                    <i class="fas fa-skull-crossbones text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-yellow-500 to-amber-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Stock faible</p>
                    <p class="text-3xl font-bold">
                        {{ collect($alertes)->where('statut', 'FAIBLE')->count() }}
                    </p>
                </div>
                <div class="p-3 bg-white/20 rounded-full">
                    <i class="fas fa-box text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- En-tête avec actions -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Alertes Stocks</h1>
            <p class="text-gray-600">Stocks nécessitant une attention immédiate</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('stocks.index') }}" 
               class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white px-6 py-3 rounded-xl font-medium flex items-center gap-2 transition-all duration-200 hover:-translate-y-1">
                <i class="fas fa-arrow-left"></i> Retour aux stocks
            </a>
        </div>
    </div>

    <!-- Alertes -->
    <div class="space-y-4">
        @forelse($alertes as $alerte)
        <div class="group relative bg-gradient-to-br from-white to-red-50 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-red-200">
            <!-- Background decoration -->
            <div class="absolute top-0 right-0 w-24 h-24 bg-red-100/20 rounded-full -translate-y-8 translate-x-8"></div>
            
            <div class="relative z-10">
                <div class="flex flex-col lg:flex-row items-start justify-between gap-6">
                    <!-- Informations -->
                    <div class="flex-1">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-red-500 to-red-600 flex items-center justify-center">
                                    <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-xl font-bold text-gray-800">{{ $alerte->nom_variete ?? 'Variété inconnue' }}</h3>
                                    <span class="px-3 py-1 text-xs font-medium rounded-full 
                                        @if($alerte->etat == 'PERIME') bg-red-100 text-red-800
                                        @elseif($alerte->statut == 'FAIBLE') bg-yellow-100 text-yellow-800
                                        @elseif($alerte->statut == 'ANCIEN') bg-orange-100 text-orange-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ $alerte->alerte_stock ?? 'ALERTE' }}
                                    </span>
                                </div>
                                
                                <!-- Produit info -->
                                <div class="flex items-center gap-2 mb-4">
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">
                                        {{ $alerte->nom_produit ?? 'Produit inconnu' }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        #{{ $alerte->id_stock ?? 'N/A' }}
                                    </span>
                                </div>
                                
                                <!-- Stats grid -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                    <div class="text-center p-3 bg-red-50 rounded-xl">
                                        <p class="text-sm text-gray-600 mb-1">Quantité restante</p>
                                        <p class="text-2xl font-bold text-red-600">
                                            {{ number_format($alerte->quantite_stock_kg ?? 0, 1, ',', ' ') }} kg
                                        </p>
                                    </div>
                                    
                                    <div class="text-center p-3 bg-blue-50 rounded-xl">
                                        <p class="text-sm text-gray-600 mb-1">Âge du stock</p>
                                        <p class="text-2xl font-bold text-blue-600">
                                            {{ \Carbon\Carbon::parse($alerte->date_creation)->diffInDays(now()) }} jours
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            Créé le {{ \Carbon\Carbon::parse($alerte->date_creation)->format('d/m/Y') }}
                                        </p>
                                    </div>
                                    
                                    <div class="text-center p-3 bg-purple-50 rounded-xl">
                                        <p class="text-sm text-gray-600 mb-1">Statut</p>
                                        <p class="text-lg font-bold 
                                            @if($alerte->etat == 'PERIME') text-red-600
                                            @elseif($alerte->statut == 'FAIBLE') text-yellow-600
                                            @elseif($alerte->statut == 'ANCIEN') text-orange-600
                                            @else text-gray-600 @endif">
                                            {{ $alerte->etat ?? $alerte->statut }}
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Cause de l'alerte -->
                                @if($alerte->cause_perte || $alerte->etat == 'PERIME')
                                <div class="bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 rounded-r-lg p-4">
                                    <div class="flex items-start gap-2">
                                        <i class="fas fa-info-circle text-red-500 mt-0.5"></i>
                                        <div>
                                            <p class="font-medium text-red-700 mb-1">Raison de l'alerte</p>
                                            <p class="text-sm text-red-600">
                                                {{ $alerte->cause_perte ?? 'Stock périmé - À retirer immédiatement' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex flex-col gap-3">
                        @if(isset($alerte->id_recolte) && ($alerte->etat != 'PERIME'))
                        <a href="{{ route('ventes.create') }}?recolte={{ $alerte->id_recolte }}"
                           class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-medium flex items-center justify-center gap-2 transition-all duration-200 hover:-translate-y-1">
                            <i class="fas fa-shopping-cart"></i> Vendre rapidement
                        </a>
                        @endif
                        
                        @if(isset($alerte->id_recolte))
                        <a href="{{ route('recoltes.show', $alerte->id_recolte) }}"
                           class="bg-gradient-to-r from-blue-500 to-cyan-600 hover:from-blue-600 hover:to-cyan-700 text-white px-6 py-3 rounded-xl font-medium flex items-center justify-center gap-2 transition-all duration-200 hover:-translate-y-1">
                            <i class="fas fa-eye"></i> Voir la récolte
                        </a>
                        @endif
                        
                        @if(isset($alerte->id_stock))
                        <a href="{{ route('stocks.index') }}?highlight={{ $alerte->id_stock }}"
                           class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white px-6 py-3 rounded-xl font-medium flex items-center justify-center gap-2 transition-all duration-200">
                            <i class="fas fa-boxes"></i> Voir en stock
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <!-- État vide -->
        <div class="text-center py-16">
            <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-r from-green-100 to-emerald-200 rounded-full flex items-center justify-center">
                <i class="fas fa-check text-green-600 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">Aucune alerte ! 🎉</h3>
            <p class="text-gray-600 mb-4">Tous vos stocks sont en parfait état</p>
            <p class="text-sm text-gray-500 mb-8">Vous gérez efficacement votre inventaire</p>
            <a href="{{ route('stocks.index') }}" 
               class="inline-flex items-center gap-2 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-8 py-4 rounded-xl font-medium transition-all duration-200 transform hover:-translate-y-1">
                <i class="fas fa-boxes"></i> Voir tous les stocks
            </a>
        </div>
        @endforelse
    </div>

    <!-- Actions rapides -->
    @if(count($alertes) > 0)
    <div class="bg-gradient-to-r from-red-50 to-amber-50 rounded-2xl p-6 border border-red-200 mt-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-bolt text-red-600"></i> Actions recommandées
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white rounded-xl p-4 border border-red-100">
                <div class="flex items-start gap-3">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <i class="fas fa-trash text-red-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800 mb-1">Retirer les périmés</p>
                        <p class="text-sm text-gray-600">Supprimez les stocks périmés pour libérer de l'espace</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 border border-yellow-100">
                <div class="flex items-start gap-3">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <i class="fas fa-tags text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800 mb-1">Promotions urgentes</p>
                        <p class="text-sm text-gray-600">Vendez les stocks anciens avec des promotions</p>
                    </div>
                </div>
            </div>
        </di
    </div>
    @endif
</div>
@endsection