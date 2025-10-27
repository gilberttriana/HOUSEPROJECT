@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Proyectos</h2>
    @if(auth()->check() && in_array(auth()->user()->rol, ['admin','usuario']))
      <button id="btnAddProject" class="px-4 py-2 bg-primary text-white rounded">Nuevo Proyecto</button>
    @endif
  </div>

  @if(session('success'))<div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>@endif

  <div>
    @include('dashboard.proyectos.partials.projects_list')
  </div>
  {{-- incluir modal y scripts necesarios para crear proyecto en la misma página --}}
  @include('dashboard.proyectos.partials.project_modal')
  @include('dashboard.proyectos.partials.project_js')
  @if(request()->query('open'))
    <script>
      document.addEventListener('DOMContentLoaded', function(){
        try{
          const modal = document.getElementById('addProjectModal');
          if(modal){
            modal.style.display = 'flex';
            // eliminar el query param para que recargar la página no vuelva a abrir el modal
            try{ history.replaceState(null, '', window.location.pathname); }catch(e){}
          }
        }catch(e){ }
      });
    </script>
  @endif
</div>
@endsection
