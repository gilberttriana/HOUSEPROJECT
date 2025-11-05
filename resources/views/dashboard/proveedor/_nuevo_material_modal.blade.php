<!-- Modal: Nuevo Material (partial) -->
<div id="nuevoMaterialModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40">
    <div class="bg-[#21293a] rounded-xl shadow-xl w-full max-w-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-white">Agregar Material</h3>
            <button id="closeNuevoMaterial" class="text-white text-2xl leading-none">&times;</button>
        </div>
        <form action="{{ route('materiales.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            {{-- id_proveedor: usar el id_usuario del usuario autenticado --}}
            <input type="hidden" name="id_proveedor" value="{{ Auth::user()->id_usuario ?? '' }}">

            <div>
                <label class="block text-sm text-white mb-1">Nombre</label>
                <input name="nombre" required type="text" class="w-full rounded p-2 bg-[#182534] text-white border border-primary" placeholder="Nombre del material">
            </div>

            <div>
                <label class="block text-sm text-white mb-1">Precio</label>
                <input name="precio" required type="number" step="0.01" class="w-full rounded p-2 bg-[#182534] text-white border border-primary" placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm text-white mb-1">Stock</label>
                <input name="stock" type="number" class="w-full rounded p-2 bg-[#182534] text-white border border-primary" placeholder="Cantidad disponible">
            </div>

            <div>
                <label class="block text-sm text-white mb-1">Imagen</label>
                <input name="imagen" type="file" accept="image/*" class="w-full text-white">
            </div>

            <div>
                <label class="block text-sm text-white mb-1">Descripción</label>
                <textarea name="descripcion" rows="3" class="w-full rounded p-2 bg-[#182534] text-white border border-primary" placeholder="Descripción breve"></textarea>
            </div>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button" id="cancelNuevoMaterial" class="bg-gray-600 text-white px-4 py-2 rounded">Cancelar</button>
                <button type="submit" class="bg-primary text-white px-4 py-2 rounded">Guardar</button>
            </div>
        </form>
    </div>
</div>
