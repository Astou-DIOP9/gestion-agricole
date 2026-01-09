@extends('layouts.app')

@section('title', 'Tableau de Bord')
@section('icon', 'fa-chart-line')
@section('subtitle', 'Centre de contrôle de votre exploitation agricole')

@section('content')
<div x-data="{ loading: false }">
    <!-- En-tête  -->
    <div class="mb-8">
        <div class="relative bg-gradient-to-r from-green-600 to-emerald-700 rounded-2xl p-8 text-white overflow-hidden"> 
            <!-- Background decoration --> 
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-32 translate-x-32"></div> 
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-24 -translate-x-24"></div> 
            
            <div class="relative z-10">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <h1 class="text-3xl font-bold mb-2">Bienvenue sur AgriManager</h1>
                        <p class="text-green-100 opacity-90">Dashboard pour la gestion agricole</p>
                        <div class="flex items-center gap-4 mt-4">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-green-300 animate-pulse"></div>
                                <span class="text-sm">Système actif</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <!-- Date sélector -->
                        <div class="relative">
                            <input type="month" 
                                   value="{{ $mois }}"
                                   onchange="window.location.href='?mois=' + this.value"
                                   class="bg-white/20 backdrop-blur-sm border border-white/30 rounded-xl px-4 py-3 text-white placeholder-green-100 focus:outline-none focus:ring-2 focus:ring-white/50">
                            <i class="fas fa-calendar-alt absolute right-3 top-3.5 text-green-100"></i>
                        </div>
                        
                        <!-- Quick actions -->
                        <div class="flex gap-2">
                            <button @click="loading = true; location.reload()"
                                    class="bg-white/20 hover:bg-white/30 backdrop-blur-sm p-3 rounded-xl transition-all duration-200">
                                <i class="fas fa-sync-alt" :class="loading ? 'animate-spin' : ''"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques en temps réel -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Carte Chiffre d'affaires -->
        <div class="group relative bg-gradient-to-br from-white to-emerald-50 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-emerald-100">
            <div class="absolute top-4 right-4 p-3 bg-gradient-to-r from-emerald-100 to-green-100 rounded-xl">
                <i class="fas fa-euro-sign text-emerald-600 text-xl"></i>
            </div>
            <div class="mb-4">
                <p class="text-gray-500 text-sm font-medium mb-1">Chiffre d'affaires</p>
                <p class="text-3xl font-bold text-gray-800">
                    {{ number_format($kpis['chiffre_affaires'] ?? 0, 0, ',', ' ') }} FCFA 
                </p>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-emerald-500 to-green-500 rounded-full" style="width: 75%"></div>
                    </div>
                    <span class="text-sm text-emerald-600 font-medium">+12%</span>
                </div>
                <span class="text-xs text-gray-400">vs mois dernier</span>
            </div>
        </div>

        <!-- Carte Récoltes -->
        <div class="group relative bg-gradient-to-br from-white to-blue-50 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-blue-100">
            <div class="absolute top-4 right-4 p-3 bg-gradient-to-r from-blue-100 to-cyan-100 rounded-xl">
                <i class="fas fa-seedling text-blue-600 text-xl"></i>
            </div>
            <div class="mb-4">
                <p class="text-gray-500 text-sm font-medium mb-1">Quantité récoltée</p>
                <p class="text-3xl font-bold text-gray-800">
                    {{ number_format($kpis['quantite_recoltee'] ?? 0, 0, ',', ' ') }} kg
                </p>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full" style="width: 65%"></div>
                    </div>
                    <span class="text-sm text-blue-600 font-medium">+8%</span>
                </div>
                <span class="text-xs text-gray-400">ce mois</span>
            </div>
        </div>

        <!-- Carte Stocks -->
        <div class="group relative bg-gradient-to-br from-white to-amber-50 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-amber-100">
            <div class="absolute top-4 right-4 p-3 bg-gradient-to-r from-amber-100 to-yellow-100 rounded-xl">
                <i class="fas fa-boxes text-amber-600 text-xl"></i>
            </div>
            <div class="mb-4">
                <p class="text-gray-500 text-sm font-medium mb-1">Alertes stocks</p>
                <p class="text-3xl font-bold text-gray-800">
                    {{ $kpis['alertes_stocks'] ?? 0 }}
                </p>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    @if(($kpis['alertes_stocks'] ?? 0) > 5)
                    <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-medium rounded-full">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Attention
                    </span>
                    @else
                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                        <i class="fas fa-check mr-1"></i> Normal
                    </span>
                    @endif
                </div>
                <a href="{{ route('stocks.index') }}" class="text-xs text-amber-600 hover:text-amber-700 font-medium">
                    Voir →
                </a>
            </div>
        </div>

        <!-- Carte Performance -->
        <div class="group relative bg-gradient-to-br from-white to-purple-50 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-purple-100">
            <div class="absolute top-4 right-4 p-3 bg-gradient-to-r from-purple-100 to-pink-100 rounded-xl">
                <i class="fas fa-chart-line text-purple-600 text-xl"></i>
            </div>
            <div class="mb-4">
                <p class="text-gray-500 text-sm font-medium mb-1">Taux d'invendus</p>
                <p class="text-3xl font-bold text-gray-800">
                    {{ $kpis['taux_invendus'] ?? 0 }}%
                </p>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    @php
                        $taux = $kpis['taux_invendus'] ?? 0;
                        $couleur = $taux > 30 ? 'red' : ($taux > 15 ? 'yellow' : 'green');
                    @endphp
                    <div class="w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-{{ $couleur }}-500 to-{{ $couleur }}-400 rounded-full" 
                             style="width: {{ min(100, $taux) }}%"></div>
                    </div>
                    <span class="text-sm text-{{ $couleur }}-600 font-medium">
                        {{ $taux > 30 ? 'Élevé' : ($taux > 15 ? 'Moyen' : 'Bon') }}
                    </span>
                </div>
                <span class="text-xs text-gray-400">à optimiser</span>
            </div>
        </div>
    </div>

    <!-- Graphiques et métriques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Graphique récoltes -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Évolution des récoltes</h3>
                    <p class="text-sm text-gray-500">Performance sur 30 jours</p>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="window.location.href='?date_debut={{ date('Y-m-d', strtotime('-7 days')) }}&date_fin={{ date('Y-m-d') }}'"
                            class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                        7j
                    </button>
                    <button onclick="window.location.href='?date_debut={{ date('Y-m-01') }}&date_fin={{ date('Y-m-d') }}'"
                            class="px-3 py-1 text-sm  bg-gray-100 rounded-lg transition">
                        30j
                    </button>
                    <button onclick="window.location.href='?date_debut={{ date('Y-m-01', strtotime('-3 months')) }}&date_fin={{ date('Y-m-d') }}'"
                            class="px-3 py-1 text-sm bg-green-500 text-white hover:bg-gray-200 rounded-lg transition">
                        90j
                    </button>
                </div>
            </div>
            <div class="h-80">
                <canvas id="recoltesChart"></canvas>
            </div>
            <div class="mt-4 flex items-center justify-between text-sm">
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                        <span class="text-gray-600">Quantité (kg)</span>
                    </div>
                </div>
                <a href="{{ route('recoltes.index') }}" class="text-green-600 hover:text-green-700 font-medium flex items-center gap-1">
                    Voir récoltes <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>

        <!-- Top variétés -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Top des variétés</h3>
                    <p class="text-sm text-gray-500">Les plus performantes ce mois</p>
                </div>
                <a href="{{ route('recoltes.index') }}" class="text-green-600 hover:text-green-700 text-sm font-medium">
                    Voir tout
                </a>
            </div>
            
            <div class="space-y-4">
                @foreach($topVarietes as $index => $variete)
                <div class="flex items-center justify-between p-4 hover:bg-gray-50 rounded-xl transition-all duration-200 group">
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br 
                                @if($index == 0) from-yellow-400 to-amber-500
                                @elseif($index == 1) from-gray-300 to-gray-400
                                @elseif($index == 2) from-amber-700 to-amber-800
                                @else from-gray-100 to-gray-200 @endif
                                flex items-center justify-center text-white font-bold">
                                {{ $index + 1 }}
                            </div>
                            @if($index < 3)
                            <div class="absolute -top-1 -right-1 w-5 h-5 bg-{{ $index == 0 ? 'yellow' : ($index == 1 ? 'gray' : 'amber') }}-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-trophy text-xs text-white"></i>
                            </div>
                            @endif
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $variete->nom_variete ?? 'Variété' }}</p>
                            <p class="text-sm text-gray-500">{{ $variete->nom_produit ?? 'Produit' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-800 text-lg">{{ number_format($variete->ca_total ?? 0, 0, ',', ' ') }} FCFA</p>
                        <p class="text-xs text-gray-500">Chiffre d'affaires</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Alertes et actions rapides -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Alertes urgentes -->
        <div class="bg-gradient-to-br from-red-50 to-white rounded-2xl shadow-lg p-6 border border-red-100">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-red-100 rounded-xl">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Alertes urgentes</h3>
                        <p class="text-sm text-red-600">{{ count($alertesStocks) }} nécessitent attention</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-medium">
                    <i class="fas fa-clock mr-1"></i> En temps réel
                </span>
            </div>
            
            <div class="space-y-3 max-h-64 overflow-y-auto pr-2">
                @forelse($alertesStocks as $alerte)
                <div class="bg-white border border-red-100 rounded-xl p-4 hover:shadow-md transition-all duration-200">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3">
                            <div class="mt-1">
                                <div class="w-3 h-3 rounded-full bg-red-500 animate-pulse"></div>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $alerte->nom_variete ?? 'Variété inconnue' }}</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $alerte->quantite_stock_kg ?? 0 }} kg restants • {{ $alerte->alerte_stock ?? 'Alerte stock' }}
                                </p>
                                <div class="flex items-center gap-3 mt-2">
                                    <span class="text-xs px-2 py-1 bg-red-50 text-red-700 rounded-full">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ \Carbon\Carbon::parse($alerte->date_creation ?? now())->diffForHumans() }}
                                    </span>
                                    @if($alerte->etat == 'PERIME')
                                    <span class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded-full">
                                        <i class="fas fa-skull-crossbones mr-1"></i> Périmé
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if(isset($alerte->id_recolte))
                        <a href="{{ route('ventes.create') }}?recolte={{ $alerte->id_recolte }}"
                           class="text-green-600 hover:text-green-700 p-2 hover:bg-green-50 rounded-lg transition">
                            <i class="fas fa-shopping-cart"></i>
                        </a>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-green-600 text-2xl"></i>
                    </div>
                    <p class="text-gray-600">Aucune alerte urgente</p>
                    <p class="text-sm text-gray-500 mt-1">Tous vos stocks sont en bon état</p>
                </div>
                @endforelse
            </div>
            
            @if(count($alertesStocks) > 0)
            <div class="mt-6 pt-4 border-t border-red-100">
                <a href="{{ route('stocks.index') }}" 
                   class="w-full bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white py-3 px-4 rounded-xl font-medium flex items-center justify-center gap-2 transition-all duration-200">
                    <i class="fas fa-boxes"></i>
                    Gérer les stocks ({{ count($alertesStocks) }} alertes)
                </a>
            </div>
            @endif
        </div>

        <!-- Actions rapides & Ventes du jour -->
        <div class="bg-gradient-to-br from-blue-50 to-white rounded-2xl shadow-lg p-6 border border-blue-100">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-blue-100 rounded-xl">
                        <i class="fas fa-bolt text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Actions rapides</h3>
                        <p class="text-sm text-blue-600">Effectuez vos tâches rapidement</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Aujourd'hui</p>
                    <p class="font-bold text-gray-800">{{ date('d/m/Y') }}</p>
                </div>
            </div>
            
            <!-- Ventes du jour -->
            <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-5 border border-green-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Ventes du jour</p>
                        <p class="text-2xl font-bold text-gray-800">
                            {{ number_format($ventesJour['montant'] ?? 0, 0, ',', ' ') }} FCFA
                        </p>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $ventesJour['nombre'] ?? 0 }} transaction(s)
                        </p>
                    </div>
                    <div class="relative">
                        <div class="w-20 h-20 rounded-full bg-gradient-to-r from-green-200 to-emerald-300 flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-green-600 text-2xl"></i>
                        </div>
                        <div class="absolute -top-1 -right-1 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <span class="text-white text-xs font-bold">{{ $ventesJour['nombre'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Actions rapides grid -->
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('recoltes.create') }}" 
                   class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white p-4 rounded-xl transition-all duration-200 transform hover:-translate-y-1 group">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mb-2">
                            <i class="fas fa-seedling text-xl"></i>
                        </div>
                        <span class="font-medium">Nouvelle récolte</span>
                        <span class="text-xs opacity-90 mt-1">Enregistrer</span>
                    </div>
                </a>
                
                <a href="{{ route('ventes.create') }}" 
                   class="bg-gradient-to-r from-blue-500 to-cyan-600 hover:from-blue-600 hover:to-cyan-700 text-white p-4 rounded-xl transition-all duration-200 transform hover:-translate-y-1 group">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mb-2">
                            <i class="fas fa-cash-register text-xl"></i>
                        </div>
                        <span class="font-medium">Nouvelle vente</span>
                        <span class="text-xs opacity-90 mt-1">Transaction</span>
                    </div>
                </a>
                
                <a href="{{ route('stocks.index') }}" 
                   class="bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white p-4 rounded-xl transition-all duration-200 transform hover:-translate-y-1 group">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mb-2">
                            <i class="fas fa-boxes text-xl"></i>
                        </div>
                        <span class="font-medium">Vérifier stocks</span>
                        <span class="text-xs opacity-90 mt-1">Inventaire</span>
                    </div>
                </a>
                
                <a href="{{ route('produits.index') }}" 
                   class="bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white p-4 rounded-xl transition-all duration-200 transform hover:-translate-y-1 group">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mb-2">
                            <i class="fas fa-apple-alt text-xl"></i>
                        </div>
                        <span class="font-medium">Produits</span>
                        <span class="text-xs opacity-90 mt-1">Gestion</span>
                    </div>
                </a>
            </div>
            
            <!-- Dernière mise à jour -->
            <div class="mt-6 pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-history"></i>
                        <span>Dernière mise à jour</span>
                    </div>
                    <span class="font-medium">{{ now()->format('H:i:s') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Graphique des récoltes avec design premium
    const recoltesCtx = document.getElementById('recoltesChart').getContext('2d');
    const gradient = recoltesCtx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(76, 175, 80, 0.3)');
    gradient.addColorStop(1, 'rgba(76, 175, 80, 0.05)');
    
    const recoltesChart = new Chart(recoltesCtx, {
        type: 'line',
        data: {
            labels: [
                @foreach($recoltesChart as $recolte)
                    "{{ \Carbon\Carbon::parse($recolte->date_recolte)->format('d/m') }}",
                @endforeach
            ],
            datasets: [{
                label: 'Quantité récoltée',
                data: [
                    @foreach($recoltesChart as $recolte)
                        {{ $recolte->quantite_totale ?? 0 }},
                    @endforeach
                ],
                borderColor: '#10B981',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#10B981',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#10B981',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return `Quantité: ${context.parsed.y} kg`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        color: '#6B7280'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        color: '#6B7280'
                    }
                }
            }
        }
    });
    
    // Animation pour les cartes
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.group');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.boxShadow = '0 20px 40px -10px rgba(0, 0, 0, 0.1)';
            });
            card.addEventListener('mouseleave', function() {
                this.style.boxShadow = '';
            });
        });
    });
</script>
@endpush
@endsection