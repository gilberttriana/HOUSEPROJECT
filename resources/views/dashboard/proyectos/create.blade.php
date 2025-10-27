@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-2xl font-bold">Proyectos</h2>
    <div>
      <button id="btnAddProject" class="inline-flex items-center gap-2 bg-primary text-white font-semibold py-2 px-4 rounded-lg hover:bg-primary/90">+
        Agregar Proyecto
      </button>
    </div>
  </div>

  @if($errors->any())<div class="mb-4 text-red-600">{{ $errors->first() }}</div>@endif

  {{-- Lista de proyectos --}}
  @include('dashboard.proyectos.partials.projects_list')

  {{-- Modal de creación (partial) --}}
  @include('dashboard.proyectos.partials.project_modal', ['materialsList' => $materialsList ?? collect()])
</div>
@endsection

@section('scripts')
  @include('dashboard.proyectos.partials.project_js')
@endsection
