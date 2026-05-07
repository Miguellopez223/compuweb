<?php

namespace App\Models;

use App\Models\Scopes\TiendaScope;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'tienda_id',
        'nombre',
        'ci_nit',
        'telefono',
        'email',
        'direccion',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TiendaScope());
    }

    public function tienda()
    {
        return $this->belongsTo(Tienda::class);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}
