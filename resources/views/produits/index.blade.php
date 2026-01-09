@extends('layouts.app')

@section('title', 'Gestion des Produits')
@section('icon', 'fa-apple-alt')
@section('subtitle', 'Gérez vos produits agricoles')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-linear-to-r from-green-500 to-emerald-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Produits</p>
                    <p class="text-3xl font-bold">{{ $produits->count() }}</p>
                </div>
                <div class="p-3 bg-white/20 rounded-full">
                    <i class="fas fa-apple-alt text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-linear-to-r from-blue-500 to-cyan-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Variétés</p>
                    <p class="text-3xl font-bold">
                        {{ $produits->sum('varietes_count') }}
                    </p>
                </div>
                <div class="p-3 bg-white/20 rounded-full">
                    <i class="fas fa-leaf text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-linear-to-r from-amber-500 to-orange-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Récoltes</p>
                    <p class="text-3xl font-bold">
                        {{ $produits->sum('recoltes_count') }}
                    </p>
                </div>
                <div class="p-3 bg-white/20 rounded-full">
                    <i class="fas fa-seedling text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- En-tête avec actions -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Produits Agricoles</h1>
            <p class="text-gray-600">Gérez les différents produits de votre exploitation</p>
        </div>

        <a href="{{ route('produits.create') }}" 
            class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-medium flex items-center gap-2 transition-all duration-200 hover:-translate-y-1">
            <i class="fas fa-plus"></i> Nouveau Produit
        </a>
    </div>

    <!-- Liste des produits -->
    @if($produits->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($produits as $produit)
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 group hover:border-green-200">
            <div class="p-6">
                <!-- En-tête de la carte -->
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 group-hover:text-green-700 transition">{{ $produit->nom_produit }}</h3>
                        @if($produit->categorie)
                        <span class="inline-block px-3 py-1 text-xs bg-green-100 text-green-700 rounded-full mt-2">
                            {{ $produit->categorie }}
                        </span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('produits.edit', $produit) }}" 
                           class="text-blue-600 hover:text-blue-800 p-2 hover:bg-blue-50 rounded-lg transition">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </div>

                <!-- Statistiques -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="text-center p-3 bg-blue-50 rounded-xl">
                        <p class="text-2xl font-bold text-blue-600">{{ $produit->varietes_count }}</p>
                        <p class="text-xs text-gray-600">Variétés</p>
                    </div>
                    <div class="text-center p-3 bg-green-50 rounded-xl">
                        <p class="text-2xl font-bold text-green-600">{{ $produit->recoltes_count }}</p>
                        <p class="text-xs text-gray-600">Récoltes</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('produits.show', $produit) }}" 
                       class="bg-linear-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white py-3 px-4 rounded-lg font-medium flex items-center justify-center gap-2 transition">
                        <i class="fas fa-eye"></i> Voir
                    </a>
                    <a href="{{ route('varietes.index') }}?produit={{ $produit->id_produit }}" 
                       class="bg-linear-to-r from-blue-500 to-cyan-600 hover:from-blue-600 hover:to-cyan-700 text-white py-3 px-4 rounded-lg font-medium flex items-center justify-center gap-2 transition">
                        <i class="fas fa-leaf"></i> Variétés
                    </a>
                </div>

                <!-- Suppression conditionnelle -->
                @if($produit->varietes_count == 0)
                <form action="{{ route('produits.destroy', $produit) }}" 
                      method="POST" 
                      class="mt-4 pt-4 border-t border-gray-100"
                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full text-red-600 hover:text-red-700 hover:bg-red-50 py-2 px-4 rounded-lg font-medium flex items-center justify-center gap-2 transition">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </form>
                @else
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-500 text-center">
                        <i class="fas fa-info-circle mr-1"></i> Non supprimable ({{ $produit->varietes_count }} variétés associées)
                    </p>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <!-- État vide -->
    <div class="text-center py-16">
        <div class="w-24 h-24 mx-auto mb-6 bg-linear-to-r from-green-100 to-emerald-200 rounded-full flex items-center justify-center">
            <i class="fas fa-apple-alt text-green-600 text-3xl"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">Aucun produit enregistré</h3>
        <p class="text-gray-600 mb-8">Commencez par créer votre premier produit agricole</p>
        <a href="{{ route('produits.create') }}" 
           class="inline-flex items-center gap-2 bg-linear-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-8 py-4 rounded-xl font-medium transition-all duration-200 transform hover:-translate-y-1">
            <i class="fas fa-plus"></i> Créer un produit
        </a>
    </div>
    @endif
</div>
@endsection