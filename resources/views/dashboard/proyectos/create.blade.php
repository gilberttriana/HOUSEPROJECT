@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
  <h2 class="text-2xl font-bold mb-4">Nuevo Proyecto</h2>
  @if($errors->any())<div class="mb-4 text-red-600">{{ $errors->first() }}</div>@endif
  <form method="POST" action="{{ route('proyectos.store') }}">
    @csrf
    <div class="mb-3">
      <label class="block text-sm">Nombre</label>
      <input name="nombre" class="w-full border px-3 py-2 rounded" required />
    </div>
    <div class="mb-3">
      <label class="block text-sm">Descripción</label>
      <textarea name="descripcion" class="w-full border px-3 py-2 rounded"></textarea>
    </div>
    <div class="flex justify-end">
      <button class="px-4 py-2 bg-primary text-white rounded">Crear</button>
    </div>
  </form>
</div>
@endsection
