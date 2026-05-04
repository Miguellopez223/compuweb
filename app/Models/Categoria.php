<?php

namespace App\Models;

use App\Models\Scopes\TiendaScope;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $fillable = ['tienda_id', 'nombre', 'especialista_id'];

    protected static function booted(): void
    {
        static::addGlobalScope(new TiendaScope());
    }

    public function tienda() { return $this->belongsTo(Tienda::class); }
    public function especialista() { return $this->belongsTo(User::class, 'especialista_id'); }
    public function productos() { return $this->hasMany(Producto::class); }
}
