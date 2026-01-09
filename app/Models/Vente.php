<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    protected $connection = 'oracle';
    protected $table = 'VENTE';
    protected $primaryKey = 'id_vente';
    public $timestamps = false;

    protected $fillable = [
        'date_vente',
        'quantite_vendue',
        'prix_unitaire',
        'montant_total',
        'client_type',
        'id_recolte'
    ];

    protected $casts = [
        'date_vente' => 'datetime',
        'quantite_vendue' => 'decimal:2',
        'prix_unitaire' => 'decimal:2',
        'montant_total' => 'decimal:2'
    ];

    public function recolte()
    {
        return $this->belongsTo(Recolte::class, 'id_recolte', 'id_recolte');
    }
}