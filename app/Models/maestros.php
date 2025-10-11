<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maestro extends Model
{
    protected $table = 'maestros';
    protected $primaryKey = 'id_maestro';
    protected $fillable = ['usuario_id','especialidad','experiencia','tarifa'];

    public function usuario() {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'id_usuario');
    }

   
}
