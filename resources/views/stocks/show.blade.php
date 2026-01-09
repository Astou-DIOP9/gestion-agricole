@extends('layouts.app')

@section('title', 'Stock #' . $stock->id_stock)
@section('icon', 'fa-box')
@section('subtitle', 'Détails du lot de stock')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-xl bg-gradient-to-r from-blue-500 to-cyan-600 flex items-center justify-center">
                <i class="fas fa-box text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Lot #{{ $stock->id_stock }}</h1>
                <div class="flex items-center gap-2 mt-2">
                    @php
                        $statutColor = match($stock->statut) {
                            'PERIME' => 'red',
                            'ANCIEN' => 'orange',
                            'FAIBLE' => 'yellow',
                            default => 'green'
                        };
                    @endphp
                    <span class="px-3 py-1 text-sm rounded-full bg-{{ $statutColor }}-100 text-{{ $statutColor }}-800">
                        {{ $stock->statut }}
                    </span>
                    <span class="text-gray-500">•</span>
                    <span class="text-gray-500">{{ $stock->nom_produit }}</span>
                </div>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('ventes.create') }}?recolte={{ $stock->id_recolte }}" 
               class="btn-primary flex items-center" 
               @if($stock->statut == 'PERIME' || $stock->quantite_restante <= 0) disabled @endif>
                <i class="fas fa-shopping-cart mr-2"></i>Vendre
            </a>
            <a href="{{ route('stocks.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <!-- Informations principales -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations produit -->
        <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-blue-500">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-seedling text-blue-600"></i> Informations produit
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Variété:</span>
                    <span class="font-medium text-gray-800">{{ $stock->nom_variete }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Produit:</span>
                    <span class="font-medium text-gray-800">{{ $stock->nom_produit }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Date récolte:</span>
                    <span class="font-medium text-gray-800">
                        {{ \Carbon\Carbon::parse($stock->date_recolte)->format('d/m/Y') }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Récolte ID:</span>
                    <a href="{{ route('recoltes.show', $stock->id_recolte) }}" 
                       class="text-blue-600 hover:text-blue-800 font-medium">
                        #{{ $stock->id_recolte }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Informations stock -->
        <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-green-500">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-weight text-green-600"></i> État du stock
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Quantité initiale:</span>
                    <span class="font-medium text-gray-800">
                        {{ number_format($stock->quantite_initiale ?? $stock->quantite_disponible, 1, ',', ' ') }} kg
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Quantité restante:</span>
                    <span class="font-bold text-green-600">
                        {{ number_format($stock->quantite_restante, 1, ',', ' ') }} kg
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Pourcentage restant:</span>
                    <span class="font-medium text-gray-800">
                        @php
                            $pourcentage = $stock->quantite_initiale > 0 ? 
                                ($stock->quantite_restante / $stock->quantite_initiale) * 100 : 0;
                        @endphp
                        {{ number_format($pourcentage, 1, ',', ' ') }}%
                    </span>
                </div>
                <div class="mt-4">
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-green-500 h-2.5 rounded-full" 
                             style="width: {{ min(100, $pourcentage) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations temporelles -->
        <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-orange-500">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-clock text-orange-600"></i> Informations temporelles
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Date création:</span>
                    <span class="font-medium text-gray-800">
                        {{ \Carbon\Carbon::parse($stock->date_creation)->format('d/m/Y') }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Âge du stock:</span>
                    <span class="font-medium text-gray-800">
                        @php
                            $age = now()->diffInDays(\Carbon\Carbon::parse($stock->date_creation));
                        @endphp
                        {{ $age }} jour(s)
                    </span>
                </div>
                @if($stock->date_peremption)
                <div class="flex justify-between">
                    <span class="text-gray-600">Date péremption:</span>
                    <span class="font-medium {{ $stock->statut == 'PERIME' ? 'text-red-600' : 'text-gray-800' }}">
                        {{ \Carbon\Carbon::parse($stock->date_peremption)->format('d/m/Y') }}
                    </span>
                </div>
                @endif
                @if($stock->cause_perte)
                <div class="mt-3 p-3 bg-red-50 rounded-lg">
                    <p class="text-sm font-medium text-red-800">Cause de perte:</p>
                    <p class="text-sm text-red-700">{{ $stock->cause_perte }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Historique des ventes -->
    @if(isset($ventes) && count($ventes) > 0)
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">Historique des ventes</h2>
            <p class="text-gray-600">Ventes réalisées sur ce lot</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full table-custom">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Date vente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Quantité vendue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Prix unitaire</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Montant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($ventes as $vente)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($vente->date_vente)->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-medium text-gray-800">
                                {{ number_format($vente->quantite_vendue, 1, ',', ' ') }} kg
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-blue-600">
                                {{ number_format($vente->prix_unitaire, 2, ',', ' ') }} €/kg
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-bold text-green-600">
                                {{ number_format($vente->montant_total, 2, ',', ' ') }} €
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($vente->client_type == 'REVENDEUR') bg-blue-100 text-blue-800
                                @elseif($vente->client_type == 'RESTAURANT') bg-green-100 text-green-800
                                @elseif($vente->client_type == 'PARTICULIER') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $vente->client_type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('ventes.show', $vente->id_vente) }}" 
                               class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                @php
                    $totalVendu = array_sum(array_column($ventes, 'quantite_vendue'));
                    $totalCA = array_sum(array_column($ventes, 'montant_total'));
                @endphp
                <tfoot class="bg-gray-50">
                    <tr>
                        <td class="px-6 py-4 font-bold text-gray-800">TOTAL</td>
                        <td class="px-6 py-4 font-bold text-gray-800">
                            {{ number_format($totalVendu, 1, ',', ' ') }} kg
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-800">-</td>
                        <td class="px-6 py-4 font-bold text-green-600">
                            {{ number_format($totalCA, 2, ',', ' ') }} €
                        </td>
                        <td colspan="2" class="px-6 py-4"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif

    <!-- Actions -->
    @if($stock->statut !== 'PERIME' && $stock->quantite_restante > 0)
    <div class="bg-white rounded-xl p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Actions disponibles</h3>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('ventes.create') }}?recolte={{ $stock->id_recolte }}" 
               class="btn-primary flex items-center">
                <i class="fas fa-shopping-cart mr-2"></i>Vendre ce stock
            </a>
            
            @if($stock->statut == 'ANCIEN' || $stock->statut == 'FAIBLE')
            <button onclick="showTransfertModal()"
                    class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-medium flex items-center">
                <i class="fas fa-exchange-alt mr-2"></i>Transférer
            </button>
            @endif
            
            @if($stock->statut == 'PERIME')
            <button onclick="showPerteModal()"
                    class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium flex items-center">
                <i class="fas fa-trash mr-2"></i>Déclarer perte
            </button>
            @endif
        </div>
    </div>
    @endif
</div>

<!-- Modal pour déclarer une perte -->
<div id="perteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Déclarer une perte</h3>
            <p class="text-gray-600 text-sm">Enregistrez une perte pour ce stock</p>
        </div>
        
        <form id="perteForm" method="POST" action="{{ route('stocks.declarer-perte', $stock->id_stock) }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantité perdue (kg)</label>
                    <input type="number" 
                           name="quantite_perdue" 
                           step="0.1"
                           max="{{ $stock->quantite_restante }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Raison de la perte</label>
                    <select name="cause_perte" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="PÉRIMÉ">Périmé</option>
                        <option value="DOMMAGÉ">Dommagé</option>
                        <option value="VOL">Vol</option>
                        <option value="AUTRE">Autre</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Commentaire</label>
                    <textarea name="commentaire" 
                              rows="3"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" 
                        onclick="hidePerteModal()"
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Annuler
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg">
                    Confirmer la perte
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function showPerteModal() {
        document.getElementById('perteModal').classList.remove('hidden');
    }
    
    function hidePerteModal() {
        document.getElementById('perteModal').classList.add('hidden');
    }
    
    function showTransfertModal() {
        alert('Fonctionnalité de transfert à implémenter');
    }
</script>
@endpush
@endsection