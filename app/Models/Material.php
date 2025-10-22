<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Material extends Model
{
    use HasFactory;

    protected $table = 'materiales';
    protected $primaryKey = 'id_material';
    public $timestamps = false; // usa fecha_actualizacion TIMESTAMP

    protected $fillable = [
        'id_proveedor',
        'nombre',
        'unidad',
        'precio',
        'stock',
        'imagen',
        // permitir asignación de estado/descripcion desde request
        'estado',
        'descripcion'
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id_proveedor');
    }

    // Mapear columna ESTADO o estado a atributo -> estado
    public function getEstadoAttribute()
    {
        if(array_key_exists('estado', $this->attributes)) return $this->attributes['estado'];
        if(array_key_exists('ESTADO', $this->attributes)) return $this->attributes['ESTADO'];
        return null;
    }

    public function setEstadoAttribute($value)
    {
        // Detectar la columna real en la tabla y asignar solamente a esa columna
        try{
            if (Schema::hasColumn($this->getTable(), 'ESTADO')){
                $this->attributes['ESTADO'] = $value;
                return;
            }
        }catch(\Exception $e){ /* ignore and fallback */ }
        // Si no existe la columna en mayúsculas, usar la versión en minúsculas
        $this->attributes['estado'] = $value;
    }

    // Mapear columna descripcion (mayúsculas o minúsculas)
    public function getDescripcionAttribute()
    {
        if(array_key_exists('descripcion', $this->attributes)) return $this->attributes['descripcion'];
        if(array_key_exists('DESCRIPCION', $this->attributes)) return $this->attributes['DESCRIPCION'];
        if(array_key_exists('descripcion', $this->getAttributes())) return $this->getAttributes()['descripcion'];
        if(array_key_exists('DESCRIPCION', $this->getAttributes())) return $this->getAttributes()['DESCRIPCION'];
        return null;
    }

    public function setDescripcionAttribute($value)
    {
        try{
            if (Schema::hasColumn($this->getTable(), 'DESCRIPCION')){
                $this->attributes['DESCRIPCION'] = $value;
                return;
            }
        }catch(\Exception $e){ /* ignore and fallback */ }
        $this->attributes['descripcion'] = $value;
    }
}
