@extends('layouts.app')

@section('title', 'Modifier ' . $variete->nom_variete)
@section('icon', 'fa-edit')
@section('subtitle', 'Modifier les informations de la variété')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Modifier la variété</h2>
                <p class="text-gray-600 mt-2">Mettez à jour les informations de {{ $variete->nom_variete }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('varietes.show', $variete) }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center gap-2">
                    <i class="fas fa-eye"></i> Voir
                </a>
                <a href="{{ route('varietes.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('varietes.update', $variete) }}" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Informations de la variété -->
            <div class="space-y-6">
                <!-- Produit parent -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-red-500">*</span> Produit
                    </label>
                    <select name="id_produit" 
                            required
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                        <option value="">Sélectionner un produit</option>
                        @foreach($produits as $produit)
                        <option value="{{ $produit->id_produit }}" 
                                {{ old('id_produit', $variete->id_produit) == $produit->id_produit ? 'selected' : '' }}>
                            {{ $produit->nom_produit }}
                            @if($produit->categorie)
                            ({{ $produit->categorie }})
                            @endif
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Nom de la variété -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-red-500">*</span> Nom de la variété
                    </label>
                    <input type="text" 
                           name="nom_variete" 
                           value="{{ old('nom_variete', $variete->nom_variete) }}"
                           required
                           maxlength="100"
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 text-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" 
                              rows="4"
                              class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">{{ old('description', $variete->description) }}</textarea>
                </div>

                <!-- Période optimale -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Période optimale</label>
                    <input type="text" 
                           name="periode_optimale" 
                           value="{{ old('periode_optimale', $variete->periode_optimale) }}"
                           maxlength="50"
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                </div>

                <!-- Informations actuelles -->
                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl p-6 border border-blue-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600"></i> Informations actuelles
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Récoltes enregistrées</p>
                            <p class="text-lg font-bold text-gray-800">
                                {{ DB::connection('oracle')->selectOne("SELECT COUNT(*) as count FROM RECOLTE WHERE id_variete = ?", [$variete->id_variete])->count ?? 0 }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Date création</p>
                            <p class="text-sm font-medium text-gray-800">
                                {{ optional($variete->created_at)->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('varietes.show', $variete) }}" 
                   class="px-6 py-3 border border-gray-300 rounded-xl font-medium text-gray-700 hover:bg-gray-50 transition flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" 
                        class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-8 py-3 rounded-xl font-medium transition-all duration-200 transform hover:-translate-y-1 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Mettre à jour
                </button>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    @if(DB::connection('oracle')->selectOne("SELECT COUNT(*) as count FROM RECOLTE WHERE id_variete = ?", [$variete->id_variete])->count == 0)
    <div class="mt-8 bg-gradient-to-r from-red-50 to-rose-50 rounded-2xl p-6 border border-red-100">
        <h3 class="text-lg font-semibold text-red-800 mb-4 flex items-center gap-2">
            <i class="fas fa-exclamation-triangle text-red-600"></i> Zone de danger
        </h3>
        
        <div class="space-y-4">
            <p class="text-red-700">
                Cette action est irréversible. Une fois supprimée, la variété ne pourra plus être récupérée.
                Cette variété n'a aucune récolte associée et peut être supprimée.
            </p>
            
            <form action="{{ route('varietes.destroy', $variete) }}" 
                  method="POST" 
                  onsubmit="return confirm('Êtes-vous ABSOLUMENT sûr de vouloir supprimer cette variété ? Cette action est irréversible.')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white px-6 py-3 rounded-xl font-medium transition flex items-center gap-2">
                    <i class="fas fa-trash"></i> Supprimer définitivement
                </button>
            </form>
        </div>
    </div>
    @else
    <div class="mt-8 bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl p-6 border border-amber-100">
        <h3 class="text-lg font-semibold text-amber-800 mb-4 flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-amber-600"></i> Information importante
        </h3>
        <p class="text-amber-700">
            Cette variété ne peut pas être supprimée car elle possède 
            {{ DB::connection('oracle')->selectOne("SELECT COUNT(*) as count FROM RECOLTE WHERE id_variete = ?", [$variete->id_variete])->count }} 
            récoltes associées. Pour la supprimer, vous devez d'abord supprimer ou transférer toutes ses récoltes.
        </p>
    </div>
    @endif
</div>
@endsection