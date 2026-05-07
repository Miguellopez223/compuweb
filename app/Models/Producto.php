<?php

namespace App\Models;

use App\Models\Scopes\TiendaScope;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'tienda_id', 'categoria_id', 'nombre', 'sku',
        'descripcion', 'imagen', 'precio', 'precio_costo', 'stock', 'stock_minimo',
        'unidad_medida', 'estado',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'precio_costo' => 'decimal:2',
        'stock'  => 'integer',
        'stock_minimo' => 'integer',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TiendaScope());
    }

    public function getImagenUrlAttribute(): ?string
    {
        if (!$this->imagen) return null;
        if (str_starts_with($this->imagen, 'http')) return $this->imagen;
        // paths guardados via Storage disk (e.g. "productos/archivo.jpg")
        if (!str_starts_with($this->imagen, 'uploads/')) {
            return asset('storage/' . $this->imagen);
        }
        // paths guardados directamente en public/ (e.g. "uploads/productos/archivo.jpg")
        return asset($this->imagen);
    }

    public function tienda() { return $this->belongsTo(Tienda::class); }
    public function categoria() { return $this->belongsTo(Categoria::class); }
    public function detallesVenta() { return $this->hasMany(DetalleVenta::class); }
    public function movimientos() { return $this->hasMany(MovimientoInventario::class); }
    public function atributos() { return $this->hasMany(AtributoProducto::class); }
}
