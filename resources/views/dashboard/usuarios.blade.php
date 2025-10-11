@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
  <header class="flex justify-between items-center mb-8">
    <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Gestionar Usuarios</h2>
  </header>
  <div class="mb-6">
    <div class="relative">
      <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
      <input class="w-full pl-10 pr-4 py-2 rounded-lg bg-white dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 focus:ring-primary focus:border-primary text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500" placeholder="Buscar usuarios por nombre o correo electrónico" type="text"/>
    </div>
  </div>
  <div class="space-y-12">
   @foreach($roles as $rol)
  <div>
    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
      {{ ucfirst($rol) }}{{ $rol == 'maestro' ? 's de Obra' : ($rol == 'usuario' ? 's' : 'es') }}
    </h3>
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
      <thead class="bg-gray-50 dark:bg-gray-800/50">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nombre</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Correo Electrónico</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estado</th>
          <th class="relative px-6 py-3"><span class="sr-only">Acciones</span></th>
        </tr>
      </thead>
      <tbody class="bg-white dark:bg-transparent divide-y divide-gray-200 dark:divide-gray-800">
        @forelse($usuariosPorRol[$rol] as $usuario)
        <tr>
          <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
            {{ $usuario->nombre }} {{ $usuario->apellido }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
            {{ $usuario->correo }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            @php
              $estado = strtolower($usuario->estado);
              $badgeClass =
                $estado === 'activo'
                  ? 'bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300'
                  : ($estado === 'inactivo'
                    ? 'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-300'
                    : 'bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300');
            @endphp
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeClass }}">
              {{ ucfirst($usuario->estado) }}
            </span>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <button class="text-primary hover:text-primary/80">Cambiar Rol</button>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="4" class="px-6 py-4 text-center text-gray-400">No hay usuarios en este rol.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endforeach
  </div>
</div>
@endsection