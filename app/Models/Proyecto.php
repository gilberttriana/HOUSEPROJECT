<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use HasFactory;

    protected $table = 'proyectos';
    protected $primaryKey = 'id_proyecto';
    public $timestamps = false; // la tabla usa fecha_creacion TIMESTAMP por defecto

    protected $fillable = [
        'id_usuario',
        'nombre',
        'descripcion',
        'tipo_proyecto',
        'contratista',
    'fecha_inicio_estimado',
        'presupuesto_estimado',
        'estado',
        'duracion_estimada',
        'fecha_finalizacion',
        'progreso',
        'Numero_responsable',
        'proyecto_materiales',
        'fecha_creacion'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function materiales()
    {
        return $this->belongsToMany(\App\Models\Material::class, 'proyecto_material', 'id_proyecto', 'id_material')->withPivot('cantidad')->withTimestamps();
    }
}
