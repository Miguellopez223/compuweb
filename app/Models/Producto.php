<?php

namespace App\Models;

use App\Models\Scopes\TiendaScope;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'tienda_id', 'categoria_id', 'nombre', 'sku',
        'descripcion', 'imagen', 'precio', 'stock', 'stock_minimo',
        'unidad_medida', 'estado',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'stock'  => 'integer',
        'stock_minimo' => 'integer',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TiendaScope());
    }

    public function tienda() { return $this->belongsTo(Tienda::class); }
    public function categoria() { return $this->belongsTo(Categoria::class); }
    public function detallesVenta() { return $this->hasMany(DetalleVenta::class); }
    public function movimientos() { return $this->hasMany(MovimientoInventario::class); }
    public function atributos() { return $this->hasMany(AtributoProducto::class); }
}
