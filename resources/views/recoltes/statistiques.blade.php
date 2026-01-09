@extends('layouts.app')

@section('title', 'Statistiques des Récoltes')
@section('icon', 'fa-chart-bar')
@section('subtitle', 'Analyse et suivi des récoltes')

@section('content')
{{-- <div class="space-y-6"> --}}
{{--  --}}
    En-tête avec statistiques clés
    {{-- <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8"> --}}
        Total Récoltes
        {{-- <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-6 text-white"> --}}
            {{-- <div class="flex items-center justify-between"> --}}
                {{-- <div> --}}
                    {{-- <p class="text-sm opacity-90">Total Récoltes</p> --}}
                    {{-- <p class="text-3xl font-bold">{{ $stats['nb_recoltes'] ?? 0 }}</p> --}}
                {{-- </div> --}}
                {{-- <div class="p-3 bg-white/20 rounded-full"> --}}
                    {{-- <i class="fas fa-seedling text-xl"></i> --}}
                {{-- </div> --}}
            {{-- </div> --}}
        {{-- </div> --}}
{{--  --}}
        Quantité Totale
        {{-- <div class="bg-gradient-to-r from-blue-500 to-cyan-600 rounded-2xl p-6 text-white"> --}}
            {{-- <div class="flex items-center justify-between"> --}}
                {{-- <div> --}}
                    {{-- <p class="text-sm opacity-90">Quantité Totale</p> --}}
                    {{-- <p class="text-3xl font-bold"> --}}
                        {{-- {{ number_format($stats['quantite_recoltee'] ?? 0, 1, ',', ' ') }} kg --}}
                    {{-- </p> --}}
                {{-- </div> --}}
                {{-- <div class="p-3 bg-white/20 rounded-full"> --}}
                    {{-- <i class="fas fa-weight text-xl"></i> --}}
                {{-- </div> --}}
            {{-- </div> --}}
        {{-- </div> --}}
{{--  --}}
        Stock Disponible
    {{-- <a href="{{ route('stocks.index') }}" --}}
         {{-- class="bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white p-4 rounded-xl transition-all duration-200 transform hover:-translate-y-1 group"> --}}
         {{-- <div class="flex flex-col items-center text-center"> --}}
             {{-- <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mb-2"> --}}
                {{-- <i class="fas fa-boxes text-xl"></i> --}}
            {{-- </div> --}}
            {{-- <span class="font-medium">Vérifier stocks</span> --}}
            {{-- <span class="text-xs opacity-90 mt-1">Inventaire</span> --}}
         {{-- </div> --}}
    {{-- </a> --}}
{{--  --}}
{{--  --}}
{{--      --}}
{{--  --}}
    Graphiques
    {{-- <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white p-6 rounded-2xl shadow-lg"> --}}
        {{-- <div> --}}
            {{-- <h2 class="text-lg font-semibold mb-4">Ventes par produit</h2> --}}
            {{-- <canvas id="produitChart"></canvas> --}}
        {{-- </div> --}}
{{--  --}}
        {{-- <div> --}}
            {{-- <h2 class="text-lg font-semibold mb-4">Production par variété</h2> --}}
            {{-- <canvas id="varieteChart"></canvas> --}}
        {{-- </div> --}}
    {{-- </div> --}}
{{--  --}}
{{-- </div> --}}
{{--  --}}
Chart.js CDN
{{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
{{--  --}}
{{-- <script> --}}
    // const produits = @json($produits ?? []);
    // const varietes = @json($varietes ?? []);
// 
    Bar chart Produits
    // new Chart(document.getElementById('produitChart'), {
        // type: 'bar',
        // data: {
            // labels: produits.map(p => p.nom_produit),
            // datasets: [{
                // label: 'Quantité vendue (kg)',
                // data: produits.map(p => p.quantite_vendue ?? 0),
                // backgroundColor: 'rgba(37, 99, 235, 0.7)', // Tailwind blue-600
                // borderColor: 'rgba(37, 99, 235, 1)',
                // borderWidth: 1
            // }]
        // },
        // options: {
            // scales: {
                // y: { beginAtZero: true }
            // }
        // }
    // });
// 
    Pie chart Variétés
    // new Chart(document.getElementById('varieteChart'), {
        // type: 'pie',
        // data: {
            // labels: varietes.map(v => v.nom_variete),
            // datasets: [{
                // data: varietes.map(v => v.quantite_recoltee ?? 0),
                // backgroundColor: [
                    // '#34D399', '#3B82F6', '#F59E0B', '#EF4444', '#9CA3AF', '#8B5CF6'
                // ]
            // }]
        // }
    // });
{{-- </script> --}}
{{--  --}}
{{-- @endsection --}}
{{--  --}}