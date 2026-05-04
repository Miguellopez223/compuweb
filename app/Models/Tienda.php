<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tienda extends Model
{
    public function users() { return $this->hasMany(User::class); }
    public function productos() { return $this->hasMany(Producto::class); }
    public function categorias() { return $this->hasMany(Categoria::class); }
    public function ventas() { return $this->hasMany(Venta::class); }
}
