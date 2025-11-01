<?php

namespace App\Services;

use App\Models\Material;
use App\Models\Proveedor;
use App\Models\Usuario;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Servicio pequeño para encapsular la lógica de creación de materiales y cálculo de contadores
 * Esto evita que la lógica esté dispersa en el controlador y facilita pruebas/reestructuración.
 */
class MaterialService
{
    /**
     * Crear material para el usuario (proveedor) dado.
     * Retorna un array con keys: material (Model), counters (array)
     */
    public function createForUser(Request $request, Usuario $user)
    {
        // Resolver o crear proveedor asociado al usuario
        $prov = $user->proveedor;
        if (!$prov) {
            $prov = Proveedor::create([
                'usuario_id' => $user->id_usuario,
                'empresa' => trim($user->nombre . ' ' . ($user->apellido ?? '')),
            ]);
        }

        $provId = $prov->id_proveedor;

        $data = $request->only(['nombre','unidad','precio','stock','descripcion']);
        // Forzar estado pendiente (admin decidirá)
        $data['estado'] = $request->input('estado', 'pendiente');
        $data['id_proveedor'] = $provId;

        if ($request->hasFile('imagen')) {
            $files = $request->file('imagen');
            $paths = [];
            if (is_array($files)) {
                foreach ($files as $f) { $paths[] = $f->store('materiales','public'); }
            } else {
                $paths[] = $files->store('materiales','public');
            }
            $data['imagen'] = json_encode($paths);
        }

        $material = Material::create($data);

        // Calcular contadores actualizados para el proveedor
        $countMaterials = Material::where('id_proveedor', $provId)->count();
        $countProjects = Proyecto::whereHas('materiales', function($q) use ($provId){
            $q->where('id_proveedor', $provId);
        })->count();
        $countMaterialsInUse = Material::where('id_proveedor', $provId)->whereHas('proyectos')->count();

        return [
            'material' => $material->fresh(),
            'counters' => [
                'materials' => $countMaterials,
                'projects' => $countProjects,
                'materials_in_use' => $countMaterialsInUse,
            ],
            'proveedor' => $prov,
        ];
    }
}
