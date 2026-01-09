<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variete extends Model
{
    protected $connection = 'oracle';
    protected $table = 'VARIETE';
    protected $primaryKey = 'id_variete';
    public $timestamps = false;

    protected $fillable = [
        'nom_variete',
        'description',
        'periode_optimale',
        'id_produit'
    ];

    // Relation avec Produit
    public function produit()
    {
        return $this->belongsTo(Produit::class, 'id_produit', 'id_produit');
    }

    // Test dans Tinker: >>> App\Models\Variete::with('produit')->get()
}