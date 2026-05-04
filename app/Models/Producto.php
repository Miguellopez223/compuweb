<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    public function tienda() { return $this->belongsTo(Tienda::class); }
    public function categoria() { return $this->belongsTo(Categoria::class); }
}
