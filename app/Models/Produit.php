<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    protected $connection = 'oracle';
    protected $table = 'PRODUIT';
    protected $primaryKey = 'id_produit';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'nom_produit',
        'categorie'
    ];

    protected $casts = [
        'id_produit' => 'integer'
    ];

    // Test dans Tinker: >>> App\Models\Produit::all()
}