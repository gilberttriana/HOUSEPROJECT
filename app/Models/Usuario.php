<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false; // tu tabla no tiene created_at / updated_at

    protected $fillable = [
        'nombre',
        'apellido',
        'correo',
        'contrasena',
        'google_id',
        'avatar',
        'foto',
        'rol',
    ];

    protected $hidden = [
        'contrasena',
    ];

    // Para que Auth use "contrasena" en vez de "password"
    public function getAuthPassword()
    {
        return $this->contrasena;
    }
      // Relaciones
    public function proveedor() {
        return $this->hasOne(Proveedor::class, 'usuario_id', 'id_usuario');
    }

    public function maestro() {
        return $this->hasOne(Maestro::class, 'usuario_id', 'id_usuario');
    }
    
}
