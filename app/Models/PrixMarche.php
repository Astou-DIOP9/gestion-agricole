<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrixMarche extends Model
{
    protected $connection = 'oracle';
    protected $table = 'PRIX_MARCHE';
    protected $primaryKey = 'id_prix_marche';
    public $timestamps = false;

    protected $fillable = [
        'date_prix',
        'prix_kg',
        'source',
        'tendance',
        'id_variete'
    ];

    protected $casts = [
        'date_prix' => 'datetime',
        'prix_kg' => 'decimal:2'
    ];

    public function variete()
    {
        return $this->belongsTo(Variete::class, 'id_variete', 'id_variete');
    }

    // Dernier prix pour une variété
    public static function dernierPrix($varieteId)
    {
        return self::where('id_variete', $varieteId)
            ->orderBy('date_prix', 'desc')
            ->first();
    }
}