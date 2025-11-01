<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

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

    public function proyectos()
    {
        return $this->belongsToMany(\App\Models\Proyecto::class, 'proyecto_material', 'id_material', 'id_proyecto');
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

    /**
     * Devuelve un array con las rutas de las imágenes asociadas al material.
     * Mantiene compatibilidad: si en la BD hay una cadena con una ruta, la devuelve como array de 1 elemento.
     */
    public function getImagenesAttribute()
    {
        $v = $this->attributes['imagen'] ?? null;
        if ($v === null) return [];
        // Si ya es array (no habitual), devolver tal cual
        if (is_array($v)) return $v;
        // Intentar decodificar JSON
        try{
            $decoded = json_decode($v, true);
            if (is_array($decoded)) return $decoded;
        }catch(\Throwable $e){ }
        // Si no es JSON, devolver la cadena como único elemento
        return [$v];
    }

    /**
     * URL de la imagen principal (primer elemento) usando Storage::url cuando sea posible.
     */
    public function getPrimaryImageUrlAttribute()
    {
        $imgs = $this->imagenes;
        if (empty($imgs)) return null;
        $first = $imgs[0];
        if (!$first) return null;
        // Si ya es una URL absoluta, devolverla
        if (preg_match('#^https?://#i', $first)) return $first;
        // Si existe en el disco public, devolver asset('storage/...')
        try{
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($first)){
                return asset('storage/' . ltrim($first, '/'));
            }
        }catch(\Throwable $e){ /* ignore */ }
        // Si el archivo existe en public path, devolver ruta pública
        try{
            if (file_exists(public_path($first))){
                return asset($first);
            }
        }catch(\Throwable $e){ /* ignore */ }
        // Fallback: si tiene el prefijo 'materiales/' devolver asset('storage/...') por convención
        if (strpos($first, 'materiales/') === 0){
            return asset('storage/' . ltrim($first, '/'));
        }
        // Último recurso: intentar Storage::url o devolver el valor tal cual
        try{ return Storage::url($first); }catch(\Throwable $e){ return $first; }
    }
}
