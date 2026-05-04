<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    public function tienda() { return $this->belongsTo(Tienda::class); }
    public function vendedor() { return $this->belongsTo(User::class, 'vendedor_id'); }
    public function detalles() { return $this->hasMany(DetalleVenta::class); }
}
