@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl">
  <div class="mb-6">
    <h2 class="text-2xl font-bold text-[#101922] dark:text-white">Aprobaciones de Proyectos</h2>
    <p class="mt-2 text-[#101922]/60 dark:text-white/60">Revisa y aprueba o rechaza los proyectos pendientes.</p>
  </div>
  <div class="overflow-hidden rounded-xl border border-background-light/10 bg-background-light shadow-lg dark:border-white/10 dark:bg-[#1a242d]">
    <div class="overflow-x-auto">
      <table class="w-full text-sm table-fixed" style="table-layout: fixed;">
        <thead class="bg-background-light/50 text-sm dark:bg-white/5">
          <tr>
            <th class="px-4 py-3 text-left font-medium text-[#101922] dark:text-white text-sm">Proyecto</th>
            <th class="px-4 py-3 text-left font-medium text-[#101922] dark:text-white text-sm">Solicitante</th>
            <th class="px-4 py-3 text-left font-medium text-[#101922] dark:text-white text-sm">Fecha</th>
            <th class="px-4 py-3 text-left font-medium text-[#101922] dark:text-white text-sm">Resumen</th>
            <th class="px-4 py-3 text-right font-medium text-[#101922] dark:text-white text-sm">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-background-light/10 dark:divide-white/10">
          <tr class="hover:bg-primary/10 dark:hover:bg-primary/10">
            <td class="px-4 py-3 font-medium text-[#101922] dark:text-white">Project Alpha</td>
            <td class="px-4 py-3 text-[#101922]/80 dark:text-white/80">Sophia Clark</td>
            <td class="px-4 py-3 text-[#101922]/80 dark:text-white/80">2024-01-15</td>
            <td class="max-w-xs truncate px-4 py-3 text-[#101922]/80 dark:text-white/80" style="word-wrap:break-word; overflow-wrap:break-word;">A new initiative to enhance user engagement.</td>
            <td class="px-4 py-3 text-right whitespace-nowrap">
              <div class="flex items-center justify-end gap-3">
                <button class="inline-flex h-8 items-center justify-center rounded-md px-3 text-xs font-medium text-[#101922]/80 hover:bg-primary/20 dark:text-white/80 dark:hover:bg-primary/30">Ver detalles</button>
                <button class="inline-flex h-8 items-center justify-center rounded-md bg-primary px-3 text-xs font-medium text-white hover:bg-primary/90">Aprobar</button>
                <button class="inline-flex h-8 items-center justify-center rounded-md border border-background-light/10 px-3 text-xs font-medium text-[#101922]/80 hover:bg-primary/20 dark:border-white/10 dark:text-white/80 dark:hover:bg-primary/30">Rechazar</button>
              </div>
            </td>
          </tr>
          <!-- Puedes añadir más filas aquí -->
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection