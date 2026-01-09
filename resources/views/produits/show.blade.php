@extends('layouts.app')

@section('title', $produit->nom_produit)
@section('icon', 'fa-apple-alt')
@section('subtitle', 'Détails du produit agricole')

@section('content')
<div class="space-y-8">
    <!-- En-tête -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-r from-green-500 to-emerald-600 flex items-center justify-center">
                <i class="fas fa-apple-alt text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $produit->nom_produit }}</h1>
                @if($produit->categorie)
                <div class="flex items-center gap-2 mt-2">
                    <span class="px-3 py-1 bg-green-100 text-green-700 text-sm rounded-full">
                        {{ $produit->categorie }}
                    </span>
                    <span class="text-gray-500">•</span>
                    <span class="text-gray-500">ID: #{{ $produit->id_produit }}</span>
                </div>
                @endif
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('produits.edit', $produit) }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-xl font-medium flex items-center gap-2 transition">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="{{ route('produits.index') }}" 
               class="border border-gray-300 hover:bg-gray-50 text-gray-700 px-6 py-3 rounded-xl font-medium flex items-center gap-2 transition">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl shadow p-6">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-100 rounded-xl">
                    <i class="fas fa-leaf text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Variétés</p>
                    <p class="text-3xl font-bold text-gray-800">{{ count($varietes) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow p-6">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-green-100 rounded-xl">
                    <i class="fas fa-seedling text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Récoltes totales</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats->total_recoltes ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow p-6">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-amber-100 rounded-xl">
                    <i class="fas fa-weight text-amber-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Quantité totale</p>
                    <p class="text-3xl font-bold text-gray-800">
                        {{ number_format($stats->total_quantite ?? 0, 0, ',', ' ') }} kg
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Variétés associées -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Variétés</h2>
                    <p class="text-gray-600">Variétés associées à ce produit</p>
                </div>
                <a href="{{ route('varietes.create') }}?produit={{ $produit->id_produit }}" 
                   class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2 transition">
                    <i class="fas fa-plus"></i> Nouvelle variété
                </a>
            </div>
        </div>

        @if(count($varietes) > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Période optimale</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($varietes as $variete)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-leaf text-green-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $variete->nom_variete }}</p>
                                    <p class="text-xs text-gray-500">ID: #{{ $variete->id_variete }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600 truncate max-w-xs">
                                {{ $variete->description ?? 'Aucune description' }}
                            </p>
                        </td>
                        <td class="px-6 py-4">
                            @if($variete->periode_optimale)
                            <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs rounded-full">
                                {{ $variete->periode_optimale }}
                            </span>
                            @else
                            <span class="text-gray-400 text-sm">Non spécifié</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('varietes.show', $variete->id_variete) }}" 
                                   class="text-blue-600 hover:text-blue-800 p-2 hover:bg-blue-50 rounded-lg transition">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('recoltes.create') }}?variete={{ $variete->id_variete }}" 
                                   class="text-green-600 hover:text-green-800 p-2 hover:bg-green-50 rounded-lg transition">
                                    <i class="fas fa-seedling"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12">
            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                <i class="fas fa-leaf text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Aucune variété</h3>
            <p class="text-gray-600 mb-6">Ce produit n'a pas encore de variétés associées</p>
            <a href="{{ route('varietes.create') }}?produit={{ $produit->id_produit }}" 
               class="inline-flex items-center gap-2 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-3 rounded-lg font-medium transition">
                <i class="fas fa-plus"></i> Créer une variété
            </a>
        </div>
        @endif
    </div>

    <!-- Informations statistiques -->
    @if($stats->total_recoltes > 0)
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Statistiques détaillées</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="text-center p-4 bg-green-50 rounded-xl">
                <p class="text-2xl font-bold text-green-600">{{ $stats->total_recoltes ?? 0 }}</p>
                <p class="text-sm text-gray-600">Récoltes totales</p>
            </div>
            <div class="text-center p-4 bg-blue-50 rounded-xl">
                <p class="text-2xl font-bold text-blue-600">
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
            <div class="text-center p-4 bg-purple-50 rounded-xl">
                <p class="text-2xl font-bold text-purple-600">
                    {{ number_format($stats->total_recoltes > 0 ? ($stats->total_quantite / $stats->total_recoltes) : 0, 1, ',', ' ') }} kg
                </p>
                <p class="text-sm text-gray-600">Rendement moyen</p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection