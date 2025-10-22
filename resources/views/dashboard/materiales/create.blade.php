@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
  <h2 class="text-2xl font-bold mb-4">Nuevo Material</h2>
  @if($errors->any())<div class="mb-4 text-red-600">{{ $errors->first() }}</div>@endif
  <form method="POST" action="{{ route('materiales.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
      <label class="block text-sm">Proveedor</label>
      @if(isset($options) && count($options))
        <select name="id_proveedor" class="w-full border px-3 py-2 rounded" required>
          <option value="">Selecciona un proveedor</option>
          @foreach($options as $opt)
            <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
          @endforeach
        </select>
        <div class="text-xs text-gray-500 mt-2">Si seleccionas un usuario (sin proveedor), se creará un registro de proveedor para ese usuario.</div>
      @else
        <div class="text-sm text-gray-500">No hay proveedores disponibles.</div>
      @endif
    </div>
    <div class="mb-3">
      <label class="block text-sm">Nombre</label>
      <input name="nombre" class="w-full border px-3 py-2 rounded" required />
    </div>
    <div class="mb-3">
      <label class="block text-sm">Unidad</label>
      <input name="unidad" class="w-full border px-3 py-2 rounded" />
    </div>
    <div class="mb-3">
      <label class="block text-sm">Precio</label>
      <input name="precio" type="number" step="0.01" class="w-full border px-3 py-2 rounded" required />
    </div>
    <div class="mb-3">
      <label class="block text-sm">Stock</label>
      <input name="stock" type="number" class="w-full border px-3 py-2 rounded" />
    </div>
    <div class="mb-3">
      <label class="block text-sm">Imagen (opcional)</label>
      <input name="imagen" type="file" accept="image/*" class="w-full" />
    </div>
    <div class="flex justify-end">
      <button class="px-4 py-2 bg-primary text-white rounded">Crear</button>
    </div>
  </form>
</div>
@endsection
