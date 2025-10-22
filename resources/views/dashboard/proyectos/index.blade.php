@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Proyectos</h2>
    @if(auth()->check() && in_array(auth()->user()->rol, ['admin','usuario']))
      <a href="{{ route('proyectos.create') }}" class="px-4 py-2 bg-primary text-white rounded">Nuevo Proyecto</a>
    @endif
  </div>

  @if(session('success'))<div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>@endif

  <div class="bg-white rounded shadow p-4">
    <table class="w-full text-sm">
      <thead>
        <tr class="text-left text-xs text-gray-500"><th>Nombre</th><th>Usuario</th><th>Fecha</th></tr>
      </thead>
      <tbody>
        @foreach($proyectos as $p)
        <tr class="border-t"><td class="py-2">{{ $p->nombre }}</td><td class="py-2">{{ $p->usuario->nombre ?? $p->id_usuario }}</td><td class="py-2">{{ $p->fecha_creacion }}</td></tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
