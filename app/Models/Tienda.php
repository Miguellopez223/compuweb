<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tienda extends Model
{
    protected $fillable = ['nombre', 'slug', 'telefono_principal', 'estado'];

    public function users() { return $this->hasMany(User::class); }
    public function categorias() { return $this->hasMany(Categoria::class); }
    public function productos() { return $this->hasMany(Producto::class); }
    public function ventas() { return $this->hasMany(Venta::class); }
}
