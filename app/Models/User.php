<?php

namespace App\Models;

use App\Support\Auth\HasApiTokensCompat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokensCompat, HasFactory, Notifiable;

    protected $fillable = [
        'tienda_id', 'name', 'email', 'password', 'role', 'whatsapp_number',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function tienda() { return $this->belongsTo(Tienda::class); }
    public function ventas() { return $this->hasMany(Venta::class, 'vendedor_id'); }
    public function movimientos() { return $this->hasMany(MovimientoInventario::class); }
    public function categorias() { return $this->hasMany(Categoria::class, 'especialista_id'); }
}
