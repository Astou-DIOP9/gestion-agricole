@extends('layouts.app')

@section('title', 'Gestion des Ventes')
@section('icon', 'fa-cash-register')
@section('subtitle', 'Historique des ventes agricoles')

@section('content')
<div class="space-y-6">
    <!-- Filtres -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Ventes Agricoles</h1>
                <p class="text-gray-600">Historique des ventes de produits</p>
            </div>
            
            <a href="{{ route('ventes.create') }}" 
               class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-medium flex items-center gap-2 transition-all duration-200 transform hover:-translate-y-1">
                <i class="fas fa-plus"></i> Nouvelle Vente
            </a>
        </div>

        <!-- Filtres de date -->
        <form method="GET" action="{{ route('ventes.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date début</label>
                    <input type="date" 
                           name="date_debut" 
                           value="{{ $dateDebut }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date fin</label>
                    <input type="date" 
                           name="date_fin" 
                           value="{{ $dateFin }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" 
                            class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-medium transition">
                        <i class="fas fa-filter mr-2"></i> Filtrer
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Nombre de ventes</p>
                    <p class="text-3xl font-bold">{{ count($ventes) }}</p>
                </div>
                <div class="p-3 bg-white/20 rounded-full">
                    <i class="fas fa-receipt text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-blue-500 to-cyan-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Quantité vendue</p>
                    <p class="text-3xl font-bold">{{ number_format($totalQuantite, 1, ',', ' ') }} kg</p>
                </div>
                <div class="p-3 bg-white/20 rounded-full">
                    <i class="fas fa-weight text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Chiffre d'affaires</p>
                    <p class="text-3xl font-bold">{{ number_format($totalCA, 2, ',', ' ') }} FCFA</p>
                </div>
                <div class="p-3 bg-white/20 rounded-full">
                    <i class="fas fa-euro-sign text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des ventes -->
    @if(count($ventes) > 0)
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase">Produit</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase">Quantité</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase">Prix</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase">Montant</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase">Client</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($ventes as $vente)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-500">#{{ $vente->id_vente }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-800">
                                {{ \Carbon\Carbon::parse($vente->date_vente)->format('d/m/Y') }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($vente->date_vente)->format('H:i') }}
                            </p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-800">{{ $vente->nom_variete }}</p>
                            <p class="text-xs text-gray-500">{{ $vente->nom_produit }}</p>
                            <p class="text-xs text-gray-500">
                                Récolte: {{ \Carbon\Carbon::parse($vente->date_recolte)->format('d/m/Y') }}
                            </p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-green-600">
                                {{ number_format($vente->quantite_vendue, 2, ',', ' ') }} kg
                            </span>
                            @if($vente->quantite_restante > 0)
                            <p class="text-xs text-gray-500">
                                Reste: {{ number_format($vente->quantite_restante, 2, ',', ' ') }} kg
                            </p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-blue-600">
                                {{ number_format($vente->prix_unitaire, 2, ',', ' ') }} FCFA/kg
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-purple-600">
                                {{ number_format($vente->montant_total, 2, ',', ' ') }} FCFA
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs rounded-full 
                                @if($vente->client_type == 'REVENDEUR') bg-blue-100 text-blue-800
                                @elseif($vente->client_type == 'RESTAURANT') bg-green-100 text-green-800
                                @elseif($vente->client_type == 'PARTICULIER') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $vente->client_type }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('ventes.show', $vente->id_vente) }}" 
                                   class="text-blue-600 hover:text-blue-800 p-2 hover:bg-blue-50 rounded-lg transition"
                                   title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('ventes.create') }}?recolte={{ $vente->id_recolte }}"
                                   class="text-green-600 hover:text-green-800 p-2 hover:bg-green-50 rounded-lg transition"
                                   title="Nouvelle vente">
                                    <i class="fas fa-plus"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Résumé -->
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-6 border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Résumé de la période</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">            
            <div class="text-center p-4 bg-white rounded-xl shadow-sm">
                <p class="text-sm text-gray-600">Période</p>
                <p class="font-medium text-gray-800">
                    {{ \Carbon\Carbon::parse($dateDebut ?? now()->subDays(30))->format('d/m/Y') }}-
                </p>
            </div>


            <div class="text-center p-4 bg-white rounded-xl shadow-sm">
                <p class="text-sm text-gray-600">Ventes moyennes/jour</p>
                <p class="font-medium text-gray-800">
                    @php
                        $days = max(1, \Carbon\Carbon::parse($dateFin)->diffInDays(\Carbon\Carbon::parse($dateDebut)));
                        $avgPerDay = count($ventes) / $days;
                    @endphp
                    {{ number_format($avgPerDay, 1, ',', ' ') }}
                </p>
            </div>
            <div class="text-center p-4 bg-white rounded-xl shadow-sm">
                <p class="text-sm text-gray-600">Prix moyen/kg</p>
                <p class="font-medium text-gray-800">
                    @php
                        $avgPrice = $totalQuantite > 0 ? $totalCA / $totalQuantite : 0;
                    @endphp
                    {{ number_format($avgPrice, 2, ',', ' ') }} FCFA 
                </p>
            </div>
            <div class="text-center p-4 bg-white rounded-xl shadow-sm">
                <p class="text-sm text-gray-600">CA moyen/vente</p>
                <p class="font-medium text-gray-800">
                    @php
                        $avgPerSale = count($ventes) > 0 ? $totalCA / count($ventes) : 0;
                    @endphp
                    {{ number_format($avgPerSale, 2, ',', ' ') }} FCFA
                </p>
            </div>
        </div>
    </div>
    @else
    <!-- État vide -->
    <div class="text-center py-16 bg-white rounded-2xl shadow-lg">
        <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-r from-green-100 to-emerald-200 rounded-full flex items-center justify-center">
            <i class="fas fa-cash-register text-green-600 text-3xl"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">Aucune vente enregistrée</h3>
        <p class="text-gray-600 mb-8">
            Aucune vente trouvée pour la période sélectionnée
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('ventes.create') }}" 
               class="inline-flex items-center gap-2 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-8 py-4 rounded-xl font-medium transition-all duration-200 transform hover:-translate-y-1">
                <i class="fas fa-plus"></i> Créer une vente
            </a>
            <a href="{{ route('ventes.index') }}?date_debut={{ date('Y-m-01') }}&date_fin={{ date('Y-m-d') }}" 
               class="inline-flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white px-8 py-4 rounded-xl font-medium transition">
                <i class="fas fa-calendar"></i> Voir ce mois
            </a>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    // Confirmation avant suppression
    function confirmDelete(venteId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette vente ? Cette action est irréversible.')) {
            document.getElementById('delete-form-' + venteId).submit();
        }
    }
</script>
@endpush
@endsection