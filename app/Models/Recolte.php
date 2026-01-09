<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recolte extends Model
{
    protected $connection = 'oracle';
    protected $table = 'RECOLTE';
    protected $primaryKey = 'id_recolte';
    public $timestamps = false;

    protected $fillable = [
        'date_recolte',
        'heure_recolte',
        'quantite',
        'qualite',
        'id_variete'
    ];

    protected $casts = [
        'date_recolte' => 'datetime',
        'quantite' => 'decimal:2'
    ];

    public function variete()
    {
        return $this->belongsTo(Variete::class, 'id_variete', 'id_variete');
    }

    public function ventes()
    {
        return $this->hasMany(Vente::class, 'id_recolte', 'id_recolte');
    }

    public function stock()
    {
        return $this->hasOne(Stock::class, 'id_recolte', 'id_recolte');
    }

    // Attribut calculé
    public function getQuantiteDisponibleAttribute()
    {
        $venteTotale = $this->ventes()->sum('quantite_vendue');
        return $this->quantite - $venteTotale;
    }
}