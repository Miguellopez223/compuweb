<?php

namespace App\Models;

use App\Models\Scopes\TiendaScope;
use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    protected $fillable = [
        'tienda_id', 'producto_id', 'user_id',
        'tipo', 'motivo', 'cantidad', 'precio_unitario', 'proveedor', 'referencia',
    ];

    protected $casts = [
        'cantidad'       => 'integer',
        'precio_unitario' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TiendaScope());
    }

    public function tienda() { return $this->belongsTo(Tienda::class); }
    public function producto() { return $this->belongsTo(Producto::class); }
    public function user() { return $this->belongsTo(User::class); }
}
