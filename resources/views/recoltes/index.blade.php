@extends('layouts.app')

@section('title', 'Gestion des Récoltes')
@section('icon', 'fa-seedling')
@section('subtitle', 'Suivez et gérez vos récoltes agricoles')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Récoltes</p>
                    <p class="text-3xl font-bold">{{ $recoltes->total() }}</p>
                </div>
                <div class="p-3 bg-white/20 rounded-full">
                    <i class="fas fa-seedling text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-blue-500 to-cyan-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Quantité Totale</p>
                    <p class="text-3xl font-bold">
                        @php
                            $totalQuantite = collect($recoltes->items())->sum('quantite');
                            echo number_format($totalQuantite, 1, ',', ' ') . ' kg';
                        @endphp
                    </p>
                </div>
                <div class="p-3 bg-white/20 rounded-full">
                    <i class="fas fa-weight text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Stock Disponible</p>
                    <p class="text-3xl font-bold">
                        @php
                            $stockDisponible = collect($recoltes->all())->sum(function($all) {
                                return max(0, $all->quantite_disponible ?? 0);
                            });
                            echo number_format($stockDisponible, 1, ',', ' ') . ' kg';
                        @endphp
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
            <h1 class="text-2xl font-bold text-gray-800">Gestion des Récoltes</h1>
            <p class="text-gray-600">Suivez et gérez toutes vos récoltes agricoles</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('recoltes.statistiques') }}" 
               class="bg-gradient-to-r from-blue-500 to-cyan-600 hover:from-blue-600 hover:to-cyan-700 text-white px-6 py-3 rounded-xl font-medium flex items-center gap-2 transition-all duration-200 hover:-translate-y-1">
                <i class="fas fa-chart-bar"></i> Statistiques
            </a>
            <a href="{{ route('recoltes.create') }}" 
               class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-medium flex items-center gap-2 transition-all duration-200 hover:-translate-y-1">
                <i class="fas fa-plus"></i> Nouvelle Récolte
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-2xl p-6 shadow-lg">
        <form method="GET" action="{{ route('recoltes.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Date début -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                    <input type="date" 
                           name="date_debut" 
                           value="{{ $filters['date_debut'] ?? '' }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                </div>

                <!-- Date fin -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                    <input type="date" 
                           name="date_fin" 
                           value="{{ $filters['date_fin'] ?? '' }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                </div>

                <!-- Variété -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Variété</label>
                    <select name="id_variete" 
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                        <option value="">Toutes les variétés</option>
                        @foreach($varietes as $variete)
                        <option value="{{ $variete->id_variete }}" 
                                {{ ($filters['id_variete'] ?? '') == $variete->id_variete ? 'selected' : '' }}>
                            {{ $variete->nom_variete }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex items-end gap-2">
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-medium flex items-center justify-center gap-2 transition-all duration-200 hover:-translate-y-1">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                    <a href="{{ route('recoltes.index') }}" 
                       class="px-4 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition flex items-center justify-center">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form> 
    </div>

    <!-- Tableau des récoltes -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Produit & Variété</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Quantité</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Qualité</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Disponible</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recoltes->items() as $recolte)
                    @php
                        $qualiteColor = match($recolte->qualite ?? 'NON_SPECIFIEE') {
                            'EXCELLENTE' => 'green',
                            'BONNE' => 'blue',
                            'MOYENNE' => 'yellow',
                            'FAIBLE' => 'red',
                            default => 'gray'
                        };
                        $colorClasses = [
                            'green' => 'bg-green-100 text-green-800',
                            'blue' => 'bg-blue-100 text-blue-800',
                            'yellow' => 'bg-yellow-100 text-yellow-800',
                            'red' => 'bg-red-100 text-red-800',
                            'gray' => 'bg-gray-100 text-gray-800'
                        ];
                        
                        // Vérifiez que les quantités existent
                        $quantite_disponible = $recolte->quantite_disponible ?? 0;
                        $quantite_totale = $recolte->quantite ?? 0;
                        $pourcentage_disponible = $quantite_totale > 0 ? min(100, ($quantite_disponible / $quantite_totale) * 100) : 0;
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                            #{{ $recolte->id_recolte }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                            @if(isset($recolte->date_recolte))
                                {{ \Carbon\Carbon::parse($recolte->date_recolte)->format('d/m/Y H:i') }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <p class="font-medium text-gray-800">{{ $recolte->nom_variete ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-600">{{ $recolte->nom_produit ?? 'N/A' }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="font-bold text-gray-800 mr-2">
                                    {{ number_format($quantite_totale, 1, ',', ' ') }}
                                </span>
                                <span class="text-sm text-gray-600">kg</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 text-xs rounded-full {{ $colorClasses[$qualiteColor] }}">
                                {{ $recolte->qualite ?? 'Non spécifié' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="mb-2">
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-green-600 h-2.5 rounded-full" 
                                         style="width: {{ $pourcentage_disponible }}%"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500 mt-1">
                                    <span>{{ number_format($quantite_disponible, 1, ',', ' ') }} kg dispo</span>
                                    <span>{{ $recolte->nb_ventes ?? 0 }} vente(s)</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('recoltes.show', $recolte->id_recolte) }}" 
                                   class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-50 rounded-lg transition"
                                   title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($quantite_disponible > 0)
                                <a href="{{ route('ventes.create') }}?recolte={{ $recolte->id_recolte }}" 
                                   class="text-green-600 hover:text-green-900 p-2 hover:bg-green-50 rounded-lg transition"
                                   title="Vendre">
                                    <i class="fas fa-shopping-cart"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-r from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                                    <i class="fas fa-inbox text-2xl text-gray-400"></i>
                                </div>
                                <p class="text-lg font-semibold">Aucune récolte trouvée</p>
                                <p class="text-sm mt-2">Commencez par enregistrer votre première récolte</p>
                                <a href="{{ route('recoltes.create') }}" 
                                   class="inline-flex items-center gap-2 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-8 py-3 rounded-xl font-medium mt-4 transition-all duration-200 hover:-translate-y-1">
                                    <i class="fas fa-plus"></i> Nouvelle Récolte
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($recoltes->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $recoltes->links() }}
        </div>
        @endif
    </div>
</div>
@endsection