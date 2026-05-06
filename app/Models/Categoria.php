<?php

namespace App\Models;

use App\Models\Scopes\TiendaScope;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $fillable = ['tienda_id', 'nombre'];

    protected static function booted(): void
    {
        static::addGlobalScope(new TiendaScope());
    }

    public function tienda() { return $this->belongsTo(Tienda::class); }
    public function productos() { return $this->hasMany(Producto::class); }
}
