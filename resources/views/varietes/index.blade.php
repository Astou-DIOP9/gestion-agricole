@extends('layouts.app')

@section('title', 'Gestion des Variétés')
@section('icon', 'fa-leaf')
@section('subtitle', 'Gérez les variétés de vos produits')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec filtres -->
    <div class="bg-white rounded-xl p-6 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Variétés Agricoles</h1>
                <p class="text-gray-600">Gérez les différentes variétés de vos produits</p>
            </div>
            
            <div class="flex items-center space-x-4">
                <!-- Filtre par produit -->
                <form method="GET" action="{{ route('varietes.index') }}" class="flex items-center space-x-2">
                    <select name="produit" 
                            onchange="this.form.submit()"
                            class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Tous les produits</option>
                        @foreach($produits as $produit)
                        <option value="{{ $produit->id_produit }}" {{ $produitId == $produit->id_produit ? 'selected' : '' }}>
                            {{ $produit->nom_produit }}
                        </option>
                        @endforeach
                    </select>
                    
                    @if($produitId)
                    <a href="{{ route('varietes.index') }}" 
                       class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </form>
                
                <a href="{{ route('varietes.create') }}" 
                   class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-medium flex items-center gap-2 transition-all duration-200 transform hover:-translate-y-1">
                    <i class="fas fa-plus"></i> Nouvelle Variété
                </a>
            </div>
        </div>
    </div>

    <!-- Liste des variétés -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($varietes as $variete)
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">{{ $variete->nom_variete }}</h3>
                    <p class="text-sm text-gray-600">{{ $variete->produit->nom_produit }}</p>
                </div>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('varietes.edit', $variete) }}" 
                       class="text-blue-600 hover:text-blue-900">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
            </div>

            @if($variete->description)
            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $variete->description }}</p>
            @endif

            @if($variete->periode_optimale)
            <div class="mb-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-yellow-100 text-yellow-800">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    {{ $variete->periode_optimale }}
                </span>
            </div>
            @endif

            <!-- Statistiques -->
            @php
                $stats = DB::connection('oracle')->selectOne("
                    SELECT 
                        COUNT(*) as recoltes_count,
                        SUM(quantite) as total_quantite
                    FROM RECOLTE
                    WHERE id_variete = ?
                ", [$variete->id_variete]);
            @endphp
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="text-center p-3 bg-green-50 rounded-lg">
                    <p class="text-2xl font-bold text-green-600">{{ $stats->recoltes_count ?? 0 }}</p>
                    <p class="text-xs text-gray-600">Récoltes</p>
                </div>
                <div class="text-center p-3 bg-blue-50 rounded-lg">
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($stats->total_quantite ?? 0, 0) }}</p>
                    <p class="text-xs text-gray-600">Kg total</p>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t">
                <div class="flex space-x-2">
                    <a href="{{ route('varietes.show', $variete) }}" 
                       class="flex-1 text-center px-3 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100">
                        <i class="fas fa-eye mr-1"></i> Détails
                    </a>
                    <a href="{{ route('recoltes.create') }}?variete={{ $variete->id_variete }}" 
                       class="flex-1 text-center px-3 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100">
                        <i class="fas fa-seedling mr-1"></i> Récolter
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="md:col-span-3">
            <div class="text-center py-12">
                <i class="fas fa-leaf text-4xl text-gray-400 mb-4"></i>
                <p class="text-lg text-gray-600">
                    @if($produitId)
                    Aucune variété pour ce produit
                    @else
                    Aucune variété enregistrée
                    @endif
                </p>
                <p class="text-gray-500 mt-2">Commencez par créer votre première variété</p>
                <a href="{{ route('varietes.create') }}" 
                   class="btn-primary inline-flex items-center mt-4">
                    <i class="fas fa-plus mr-2"></i>Créer une variété
                </a>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection