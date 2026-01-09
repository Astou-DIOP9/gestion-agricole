@extends('layouts.app')

@section('title', 'Gestion des Stocks')
@section('icon', 'fa-boxes')
@section('subtitle', 'Suivi et gestion de votre inventaire')

@section('content')
<div class="space-y-6">
    <!-- Statistiques stocks -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Stock total</p>
                    <p class="text-3xl font-bold text-gray-800">
                        {{ number_format($stats['total_stock'], 1, ',', ' ') }} kg
                    </p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-weight text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Lots en stock</p>
                    <p class="text-3xl font-bold text-gray-800">
                        {{ $stats['nombre_lots'] }}
                    </p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-box text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Stock ancien</p>
                    <p class="text-3xl font-bold text-gray-800">
                        {{ $stats['stock_ancien'] }}
                    </p>
                </div>
                <div class="p-3 bg-orange-100 rounded-full">
                    <i class="fas fa-clock text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Périmés</p>
                    <p class="text-3xl font-bold text-gray-800">
                        {{ $stats['stock_perime'] }}
                    </p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="bg-white rounded-2xl p-6 shadow-lg">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Inventaire des stocks</h2>
                <p class="text-gray-600">État détaillé de tous vos lots en stock</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('stocks.alertes') }}" 
                   class="bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white px-6 py-3 rounded-xl font-medium flex items-center gap-2 transition-all duration-200 hover:-translate-y-1">
                    <i class="fas fa-bell"></i>Voir les alertes
                </a>
                <a href="{{ route('recoltes.create') }}" 
                   class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-medium flex items-center gap-2 transition-all duration-200 hover:-translate-y-1">
                    <i class="fas fa-plus"></i>Nouvelle Récolte
                </a>
            </div>
        </div>
    </div>

    <!-- Tableau des stocks -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Lot</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Produit</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Date création</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Quantité restante</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">État</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Âge</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stocks as $stock)
                    @php
                        $age = now()->diffInDays(\Carbon\Carbon::parse($stock->date_creation));
                        $statutColor = match($stock->statut) {
                            'PERIME' => 'red',
                            'ANCIEN' => 'orange',
                            'FAIBLE' => 'yellow',
                            default => 'green'
                        };
                        $colorClasses = [
                            'red' => 'bg-red-100 text-red-800',
                            'orange' => 'bg-orange-100 text-orange-800',
                            'yellow' => 'bg-yellow-100 text-yellow-800',
                            'green' => 'bg-green-100 text-green-800'
                        ];
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                            #{{ $stock->id_stock }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <p class="font-medium text-gray-800">{{ $stock->nom_variete }}</p>
                                <p class="text-xs text-gray-600">{{ $stock->nom_produit }}</p>
                                <p class="text-xs text-gray-500">
                                    Récolte: {{ \Carbon\Carbon::parse($stock->date_recolte)->format('d/m/Y') }}
                                </p>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                            {{ \Carbon\Carbon::parse($stock->date_creation)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-32 bg-gray-200 rounded-full h-2.5 mr-3">
                                    <div class="bg-{{ $statutColor }}-500 h-2.5 rounded-full" 
                                         style="width: {{ min(100, ($stock->quantite_restante / $stock->quantite_disponible) * 100) }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-700">
                                    {{ number_format($stock->quantite_restante, 1, ',', ' ') }} kg
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 text-xs rounded-full {{ $colorClasses[$statutColor] }}">
                                {{ $stock->statut }}
                            </span>
                            @if($stock->etat == 'PERIME')
                            <p class="text-xs text-red-600 mt-1">{{ $stock->cause_perte ?? 'Périmé' }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                            <div class="flex items-center">
                                <i class="far fa-clock text-gray-400 mr-2"></i>
                                {{ $age }} jour(s)
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                @if($stock->statut !== 'PERIME' && $stock->quantite_restante > 0)
                                <a href="{{ route('ventes.create') }}?recolte={{ $stock->id_recolte }}" 
                                   class="text-green-600 hover:text-green-900 p-2 hover:bg-green-50 rounded-lg transition"
                                   title="Vendre">
                                    <i class="fas fa-shopping-cart"></i>
                                </a>
                                @endif
                                <a href="{{ route('recoltes.show', $stock->id_recolte) }}" 
                                   class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-50 rounded-lg transition"
                                   title="Voir récolte">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-r from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                                    <i class="fas fa-box-open text-2xl text-gray-400"></i>
                                </div>
                                <p class="text-lg font-semibold">Aucun stock enregistré</p>
                                <p class="text-sm mt-2">Les stocks sont créés automatiquement lors des récoltes</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Répartition par produit -->
    <div class="bg-white rounded-2xl p-6 shadow-lg">
        <h3 class="text-lg font-bold text-gray-800 mb-6">
            <i class="fas fa-chart-pie text-purple-600 mr-2"></i>Répartition des stocks par produit
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Liste des produits -->
            <div>
                @php
                    $produits = [];
                    foreach ($stocks as $stock) {
                        $produit = $stock->nom_produit;
                        if (!isset($produits[$produit])) {
                            $produits[$produit] = 0;
                        }
                        $produits[$produit] += $stock->quantite_restante;
                    }
                    arsort($produits);
                @endphp
                
                @foreach($produits as $nom => $quantite)
                <div class="flex items-center justify-between p-4 hover:bg-gray-50 rounded-xl transition">
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-purple-500 mr-3"></div>
                        <span class="font-medium text-gray-800">{{ $nom }}</span>
                    </div>
                    <div class="text-right">
                        <span class="font-bold text-gray-800">{{ number_format($quantite, 1, ',', ' ') }} kg</span>
                        <p class="text-xs text-gray-500">
                            {{ count(array_filter($stocks, fn($s) => $s->nom_produit === $nom)) }} lot(s)
                        </p>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Graphique simple -->
            <div class="flex items-center justify-center">
                <div class="relative w-64 h-64">
                    @php
                        $total = array_sum($produits);
                        $startAngle = 0;
                        $colors = ['#4CAF50', '#2196F3', '#FF9800', '#9C27B0', '#795548'];
                        $colorIndex = 0;
                    @endphp
                    
                    @foreach($produits as $nom => $quantite)
                        @php
                            $percentage = ($quantite / $total) * 100;
                            $angle = ($percentage / 100) * 360;
                            $color = $colors[$colorIndex % count($colors)];
                            $colorIndex++;
                        @endphp
                        <div class="absolute inset-0">
                            <div class="absolute inset-0 rounded-full border-8" 
                                 style="
                                    clip-path: polygon(50% 50%, 50% 0%, {{ 50 + 50 * sin(deg2rad($startAngle)) }}% {{ 50 - 50 * cos(deg2rad($startAngle)) }}%, 
                                                       {{ 50 + 50 * sin(deg2rad($startAngle + $angle)) }}% {{ 50 - 50 * cos(deg2rad($startAngle + $angle)) }}%);
                                    border-color: {{ $color }};
                                 ">
                            </div>
                        </div>
                        @php $startAngle += $angle; @endphp
                    @endforeach
                    
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-800">{{ count($produits) }}</p>
                            <p class="text-sm text-gray-600">Produits</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection