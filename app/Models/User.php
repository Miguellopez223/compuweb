<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'tienda_id', 'name', 'email', 'password', 'role',
        'whatsapp_number', 'visible_catalogo',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'visible_catalogo'  => 'boolean',
        ];
    }

    public function tienda() { return $this->belongsTo(Tienda::class); }
    public function ventas() { return $this->hasMany(Venta::class, 'vendedor_id'); }
    public function movimientos() { return $this->hasMany(MovimientoInventario::class); }
}
