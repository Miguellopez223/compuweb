<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    public function tienda() { return $this->belongsTo(Tienda::class); }
    public function especialista() { return $this->belongsTo(User::class, 'especialista_id'); }
    public function productos() { return $this->hasMany(Producto::class); }
}
