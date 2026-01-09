<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $connection = 'oracle';
    protected $table = 'STOCK';
    protected $primaryKey = 'id_stock';
    public $timestamps = false;

    protected $fillable = [
        'date_creation',
        'quantite_restante',
        'etat',
        'cause_perte',
        'id_recolte'
    ];

    protected $casts = [
        'date_creation' => 'datetime',
        'quantite_restante' => 'decimal:2'
    ];

    public function recolte()
    {
        return $this->belongsTo(Recolte::class, 'id_recolte', 'id_recolte');
    }

    // Scopes pour filtres
    public function scopePerime($query)
    {
        return $query->where('etat', 'PERIME');
    }

    public function scopeAncien($query, $jours = 7)
    {
        return $query->whereRaw("date_creation < SYSDATE - {$jours}");
    }
}