@extends('layouts.app')

@section('title', $variete->nom_variete)
@section('icon', 'fa-leaf')
@section('subtitle', 'Détails de la variété agricole')

@section('content')
<div class="space-y-8">
    <!-- En-tête -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-linear-to-r from-green-500 to-emerald-600 flex items-center justify-center">
                <i class="fas fa-leaf text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $variete->nom_variete }}</h1>
                <div class="flex items-center gap-2 mt-2">
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm rounded-full">
                        {{ $variete->produit->nom_produit }}
                    </span>
                    @if($variete->produit->categorie)
                    <span class="px-3 py-1 bg-green-100 text-green-700 text-sm rounded-full">
                        {{ $variete->produit->categorie }}
                    </span>
                    @endif
                    <span class="text-gray-500">•</span>
                    <span class="text-gray-500">ID: #{{ $variete->id_variete }}</span>
                </div>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('varietes.edit', $variete) }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-xl font-medium flex items-center gap-2 transition">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="{{ route('varietes.index') }}" 
               class="border border-gray-300 hover:bg-gray-50 text-gray-700 px-6 py-3 rounded-xl font-medium flex items-center gap-2 transition">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <!-- Informations principales -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Carte d'information -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Informations</h2>
            
            <div class="space-y-6">
                @if($variete->description)
                <div>
                    <p class="text-sm text-gray-600 mb-2">Description</p>
                    <p class="text-gray-800">{{ $variete->description }}</p>
                </div>
                @endif

                @if($variete->periode_optimale)
                <div>
                    <p class="text-sm text-gray-600 mb-2">Période optimale</p>
                    <span class="px-4 py-2 bg-amber-100 text-amber-700 rounded-lg font-medium">
                        {{ $variete->periode_optimale }}
                    </span>
                </div>
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-2">Produit associé</p>
                        <a href="{{ route('produits.show', $variete->produit) }}"
                           class="text-blue-600 hover:text-blue-800 font-medium">
                            {{ $variete->produit->nom_produit }}
                        </a>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-2">Catégorie</p>
                        <span class="font-medium text-gray-800">
                            {{ $variete->produit->categorie ?? 'Non spécifiée' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Statistiques</h2>
            
            <div class="space-y-4">
                <div class="text-center p-4 bg-blue-50 rounded-xl">
                    <p class="text-2xl font-bold text-blue-600">{{ $stats->total_recoltes ?? 0 }}</p>
                    <p class="text-sm text-gray-600">Récoltes totales</p>
                </div>
                
                <div class="text-center p-4 bg-green-50 rounded-xl">
                    <p class="text-2xl font-bold text-green-600">
                        {{ number_format($stats->total_quantite ?? 0, 0, ',', ' ') }} kg
                    </p>
                    <p class="text-sm text-gray-600">Quantité totale</p>
                </div>
                
                <div class="text-center p-4 bg-amber-50 rounded-xl">
                    <p class="text-2xl font-bold text-amber-600">
                        {{ number_format($stats->moyenne_quantite ?? 0, 1, ',', ' ') }} kg
                    </p>
                    <p class="text-sm text-gray-600">Moyenne par récolte</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Dernières récoltes -->
    @if(count($recoltes) > 0)
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Dernières récoltes</h2>
                    <p class="text-gray-600">Les 10 dernières récoltes de cette variété</p>
                </div>
                <a href="{{ route('recoltes.create') }}?variete={{ $variete->id_variete }}" 
                   class="bg-linear-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2 transition">
                    <i class="fas fa-plus"></i> Nouvelle récolte
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Quantité</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Qualité</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Commentaire</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recoltes as $recolte)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($recolte->date_recolte)->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-green-600">
                                {{ number_format($recolte->quantite, 1, ',', ' ') }} kg
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $qualiteColor = match($recolte->qualite) {
                                    'EXCELLENTE' => 'green',
                                    'BONNE' => 'blue',
                                    'MOYENNE' => 'yellow',
                                    'FAIBLE' => 'red',
                                    default => 'gray'
                                };
                            @endphp
                            <span class="px-3 py-1 text-xs rounded-full bg-{{ $qualiteColor }}-100 text-{{ $qualiteColor }}-800">
                                {{ $recolte->qualite ?? 'Non spécifié' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600 truncate max-w-xs">
                                {{ $recolte->commentaire ?? '-' }}
                            </p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('ventes.create') }}?recolte={{ $recolte->id_recolte }}"
                                   class="text-green-600 hover:text-green-800 p-2 hover:bg-green-50 rounded-lg transition"
                                   title="Vendre cette récolte">
                                    <i class="fas fa-cash-register"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <!-- Aucune récolte -->
    <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
        <div class="w-20 h-20 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
            <i class="fas fa-seedling text-gray-400 text-2xl"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Aucune récolte enregistrée</h3>
        <p class="text-gray-600 mb-6">Cette variété n'a pas encore fait l'objet de récoltes</p>
        <a href="{{ route('recoltes.create') }}?variete={{ $variete->id_variete }}" 
           class="inline-flex items-center gap-2 bg-linear-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-3 rounded-lg font-medium transition">
            <i class="fas fa-seedling"></i> Enregistrer une récolte
        </a>
    </div>
    @endif

    <!-- Prix du marché -->
    @if(count($prixMarche) > 0)
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Historique des prix du marché</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Prix (FCFA/kg)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Source</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Commentaire</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($prixMarche as $prix)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($prix->date_prix)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-blue-600">
                                {{ number_format($prix->prix, 2, ',', ' ') }} FCFA 
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ $prix->source ?? 'Non spécifiée' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600">{{ $prix->commentaire ?? '-' }}</p>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection