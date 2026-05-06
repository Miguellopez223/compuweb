<?php

namespace App\Models;

use App\Models\Scopes\TiendaScope;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $fillable = [
        'tienda_id', 'vendedor_id', 'codigo_pedido',
        'cliente_nombre', 'cliente_telefono', 'cliente_email',
        'cliente_direccion', 'cliente_nit',
        'total', 'metodo_pago', 'estado_venta',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TiendaScope());
    }

    public function tienda() { return $this->belongsTo(Tienda::class); }
    public function vendedor() { return $this->belongsTo(User::class, 'vendedor_id'); }
    public function detalles() { return $this->hasMany(DetalleVenta::class); }
}
