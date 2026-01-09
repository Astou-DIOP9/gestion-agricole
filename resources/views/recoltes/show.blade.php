@extends('layouts.app')

@section('title', 'Détails Récolte #'.$recolte->id_recolte)
@section('icon', 'fa-seedling')
@section('subtitle', 'Détails complets de la récolte sélectionnée')

@section('content')
<div class="space-y-6">

    {{-- En-tête --}}
    <div class="bg-white rounded-2xl shadow p-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Récolte #{{ $recolte->id_recolte }}</h1>
            <p class="text-gray-600 mt-1">{{ \Carbon\Carbon::parse($recolte->date_recolte)->format('d/m/Y H:i') }}</p>
        </div>
        <div class="text-green-600 text-xl font-semibold">
            {{ number_format($recolte->quantite ?? 0, 1, ',', ' ') }} kg
        </div>
    </div>

    {{-- Informations générales --}}
    <div class="bg-white rounded-2xl shadow p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <h2 class="font-semibold text-gray-700 mb-2">Produit & Variété</h2>
            <p class="text-gray-800">{{ $recolte->nom_produit ?? 'N/A' }}</p>
            <p class="text-gray-600">{{ $recolte->nom_variete ?? 'N/A' }}</p>
        </div>

        <div>
            <h2 class="font-semibold text-gray-700 mb-2">Qualité</h2>
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
            @endphp
            <span class="inline-block px-3 py-1 rounded-full text-sm {{ $colorClasses[$qualiteColor] }}">
                {{ $recolte->qualite ?? 'Non spécifié' }}
            </span>
        </div>

        <div>
            <h2 class="font-semibold text-gray-700 mb-2">Stock Disponible</h2>
            <p class="text-gray-800">{{ number_format($recolte->quantite_disponible ?? 0, 1, ',', ' ') }} kg</p>
        </div>
    </div>

    {{-- Progression disponibilité --}}
    <div class="bg-white rounded-2xl shadow p-6">
        @php
            $quantite_totale = $recolte->quantite ?? 0;
            $quantite_disponible = $recolte->quantite_disponible ?? 0;
            $pourcentage = $quantite_totale > 0 ? min(100, ($quantite_disponible / $quantite_totale) * 100) : 0;
        @endphp
        <h2 class="font-semibold text-gray-700 mb-3">Disponibilité du stock</h2>
        <div class="w-full bg-gray-200 rounded-full h-4">
            <div class="bg-green-600 h-4 rounded-full" style="width: {{ $pourcentage }}%"></div>
        </div>
        <div class="flex justify-between text-sm text-gray-600 mt-2">
            <span>{{ number_format($quantite_disponible, 1, ',', ' ') }} kg disponible</span>
            <span>{{ number_format($quantite_totale - $quantite_disponible, 1, ',', ' ') }} kg utilisé</span>
        </div>
    </div>

    {{-- Ventes associées --}}
    <div class="bg-white rounded-2xl shadow p-6">
        <h2 class="font-semibold text-gray-700 mb-4">Ventes associées ({{ count($ventes) }})</h2>
        @if(count($ventes) > 0)
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-2">Date vente</th>
                        <th class="px-4 py-2">Quantité vendue (kg)</th>
                        <th class="px-4 py-2">Client</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($ventes as $vente)
                    <tr>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($vente->date_vente)->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-2">{{ number_format($vente->quantite_vendue ?? 0, 1, ',', ' ') }}</td>
                        <td class="px-4 py-2">{{ $vente->client_nom ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-gray-500">Aucune vente enregistrée pour cette récolte.</p>
        @endif
    </div>

    {{-- Stock détaillé --}}
    <div class="bg-white rounded-2xl shadow p-6">
        <h2 class="font-semibold text-gray-700 mb-4">Détails du stock</h2>
        @if($stock)
        <ul class="list-disc list-inside text-gray-800">
            <li>Emplacement : {{ $stock->emplacement ?? 'Non renseigné' }}</li>
            <li>Quantité totale en stock : {{ number_format($stock->quantite_stock ?? 0, 1, ',', ' ') }} kg</li>
            <li>Date dernière mise à jour : {{ \Carbon\Carbon::parse($stock->date_maj ?? now())->format('d/m/Y') }}</li>
        </ul>
        @else
        <p class="text-gray-500">Aucune information de stock disponible.</p>
        @endif
    </div>

    <div class="flex justify-between gap-4">
        <a href="{{ route('recoltes.index') }}" 
           class="bg-gray-200 text-gray-800 px-6 py-3 rounded-xl hover:bg-gray-300 transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
        @if(($recolte->quantite_disponible ?? 0) > 0)
        <a href="{{ route('ventes.create') }}?recolte={{ $recolte->id_recolte }}" 
           class="bg-green-500 text-white px-6 py-3 rounded-xl hover:bg-green-600 transition flex items-center gap-2">
            <i class="fas fa-shopping-cart"></i> Effectuer une vente
        </a>
        @endif
    </div>
</div>
@endsection
