@extends('layouts.app-maestro')

@section('title', 'Dashboard Maestro de Obras')

@section('content')
    <header class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-text-dark">Dashboard del Maestro de Obras</h1>
        <div class="flex items-center gap-4">
            <button class="relative">
                <span class="material-icons text-text-secondary-dark">notifications</span>
                <span class="absolute top-0 right-0 h-2 w-2 bg-red-500 rounded-full"></span>
            </button>
            <div class="flex items-center gap-3">
                <img alt="Avatar del maestro de obras" class="h-10 w-10 rounded-full" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBeMiT9H-3XnN93ARlYtKwW2_qTSmFhzMoyICcwVunF2m217rZ8RvCIFf_n3aOheK3jusLZTVHKjJwAPQ-q8taZlLswO5Q0h95-1_op5p9mNW4SzpHmULLJhw0f-OfbqSy5j_gSCocFQrZA52ZhH708aJvWGUEEjjMsTN9PGtrC6-rY4yyZTyHJoRnFEzcW6JuJsTNPlTu_XgSpfewV_qQWOhCuuI0cwRNEH_XO55hDGwmDrR1jhzwQpiihVx2HCfDQ0oa74VyraDA"/>
                <div>
                    <p class="font-semibold text-text-dark">{{ Auth::user()->name ?? 'Juan Pérez' }}</p>
                    <p class="text-sm text-text-secondary-dark">Maestro de Obras</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-card-dark p-6 rounded-lg shadow-md flex items-center gap-4 border border-border-dark">
            <div class="bg-blue-900/50 p-3 rounded-full">
                <span class="material-icons text-blue-400">engineering</span>
            </div>
            <div>
                <p class="text-text-secondary-dark">Proyectos Activos</p>
                <p class="text-2xl font-bold text-text-dark">5</p>
            </div>
        </div>
         {{-- Este es un comentario de Blade

        <div class="bg-card-dark p-6 rounded-lg shadow-md flex items-center gap-4 border border-border-dark">
            <div class="bg-green-900/50 p-3 rounded-full">
                <span class="material-icons text-green-400">check_circle</span>
            </div>
            <div>
                <p class="text-text-secondary-dark">Tareas Completadas</p>
                <p class="text-2xl font-bold text-text-dark">125</p>
            </div>
        </div>
        --}}
          {{-- Este es un comentario de Blade

        <div class="bg-card-dark p-6 rounded-lg shadow-md flex items-center gap-4 border border-border-dark">
            <div class="bg-yellow-900/50 p-3 rounded-full">
                <span class="material-icons text-yellow-400">pending</span>
            </div>
            <div>
                <p class="text-text-secondary-dark">Tareas Pendientes</p>
                <p class="text-2xl font-bold text-text-dark">32</p>
            </div>
        </div>
        --}}
        <div class="bg-card-dark p-6 rounded-lg shadow-md flex items-center gap-4 border border-border-dark">
            <div class="bg-red-900/50 p-3 rounded-full">
                <span class="material-icons text-red-400">warning</span>
            </div>
            <div>
                <p class="text-text-secondary-dark">Alertas de Seguridad</p>
                <p class="text-2xl font-bold text-text-dark">3</p>
            </div>
        </div>
    </div>

    <!-- Grid de Contenido Principal -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Resumen de Proyectos -->
        <div class="lg:col-span-2 bg-card-dark p-6 rounded-lg shadow-md border border-border-dark">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-text-dark">Resumen de Proyectos</h3>
                <button class="text-primary font-semibold flex items-center gap-1 hover:text-yellow-400 transition-colors">
                    Ver todos <span class="material-icons text-sm">arrow_forward</span>
                </button>
            </div>

            <div class="space-y-4">
                <!-- Proyecto 1 -->
                <div class="p-4 rounded-lg border border-border-dark hover:border-primary/50 transition-colors">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-semibold text-text-dark">Edificio Residencial "El Roble"</h4>
                            <p class="text-sm text-text-secondary-dark">Av. Principal 123, Ciudad Capital</p>
                        </div>
                        <span class="text-sm font-medium text-green-400 bg-green-900/50 px-2 py-1 rounded-full">En Progreso</span>
                    </div>
                    <div class="mt-4">
                        <p class="text-sm text-text-secondary-dark mb-1">Progreso</p>
                        <div class="w-full bg-gray-700 rounded-full h-2.5">
                            <div class="bg-primary h-2.5 rounded-full" style="width: 75%"></div>
                        </div>
                        <p class="text-right text-sm font-medium text-text-dark mt-1">75%</p>
                    </div>
                </div>

                <!-- Proyecto 2 -->
                <div class="p-4 rounded-lg border border-border-dark hover:border-primary/50 transition-colors">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-semibold text-text-dark">Centro Comercial "Plaza del Sol"</h4>
                            <p class="text-sm text-text-secondary-dark">Calle Secundaria 456, Ciudad Capital</p>
                        </div>
                        <span class="text-sm font-medium text-blue-400 bg-blue-900/50 px-2 py-1 rounded-full">Iniciando</span>
                    </div>
                    <div class="mt-4">
                        <p class="text-sm text-text-secondary-dark mb-1">Progreso</p>
                        <div class="w-full bg-gray-700 rounded-full h-2.5">
                            <div class="bg-primary h-2.5 rounded-full" style="width: 20%"></div>
                        </div>
                        <p class="text-right text-sm font-medium text-text-dark mt-1">20%</p>
                    </div>
                </div>

                <!-- Proyecto 3 -->
                <div class="p-4 rounded-lg border border-border-dark hover:border-primary/50 transition-colors">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-semibold text-text-dark">Remodelación Oficinas "CorpCenter"</h4>
                            <p class="text-sm text-text-secondary-dark">Zona Financiera, Oficina 301</p>
                        </div>
                        <span class="text-sm font-medium text-gray-400 bg-gray-700 px-2 py-1 rounded-full">Pausado</span>
                    </div>
                    <div class="mt-4">
                        <p class="text-sm text-text-secondary-dark mb-1">Progreso</p>
                        <div class="w-full bg-gray-700 rounded-full h-2.5">
                            <div class="bg-gray-500 h-2.5 rounded-full" style="width: 50%"></div>
                        </div>
                        <p class="text-right text-sm font-medium text-text-dark mt-1">50%</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Este es un comentario de Blade --}}

        <!-- Panel Lateral Derecho -->
        <div class="space-y-6">
            <!-- Próximas Tareas -->
            <div class="bg-card-dark p-6 rounded-lg shadow-md border border-border-dark">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-text-dark">Próximas Tareas</h3>
                    <button class="text-primary font-semibold flex items-center gap-1 hover:text-yellow-400 transition-colors">
                        Ver todas <span class="material-icons text-sm">arrow_forward</span>
                    </button>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-3 rounded-lg border border-border-dark hover:border-primary/50 transition-colors">
                        <div class="bg-blue-900/50 p-2 rounded-full">
                            <span class="material-icons text-blue-400 text-sm">foundation</span>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-text-dark text-sm">Cimentación Nivel 2</p>
                            <p class="text-xs text-text-secondary-dark">Hoy - 14:00</p>
                        </div>
                        <span class="material-icons text-yellow-400 text-sm">schedule</span>
                    </div>

                    <div class="flex items-center gap-3 p-3 rounded-lg border border-border-dark hover:border-primary/50 transition-colors">
                        <div class="bg-green-900/50 p-2 rounded-full">
                            <span class="material-icons text-green-400 text-sm">electric_bolt</span>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-text-dark text-sm">Instalación Eléctrica</p>
                            <p class="text-xs text-text-secondary-dark">Mañana - 09:00</p>
                        </div>
                        <span class="material-icons text-yellow-400 text-sm">schedule</span>
                    </div>

                    <div class="flex items-center gap-3 p-3 rounded-lg border border-border-dark hover:border-primary/50 transition-colors">
                        <div class="bg-purple-900/50 p-2 rounded-full">
                            <span class="material-icons text-purple-400 text-sm">plumbing</span>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-text-dark text-sm">Instalación Sanitaria</p>
                            <p class="text-xs text-text-secondary-dark">15 Dic - 10:30</p>
                        </div>
                        <span class="material-icons text-yellow-400 text-sm">schedule</span>
                    </div>
                </div>
            </div>

            <!-- Alertas de Seguridad -->
            <div class="bg-card-dark p-6 rounded-lg shadow-md border border-border-dark">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-text-dark">Alertas de Seguridad</h3>
                    <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">3 Activas</span>
                </div>

                <div class="space-y-3">
                    <div class="flex items-start gap-3 p-3 rounded-lg border border-red-500/50 bg-red-900/20">
                        <span class="material-icons text-red-400 text-sm">warning</span>
                        <div>
                            <p class="font-medium text-text-dark text-sm">Andamio Inestable</p>
                            <p class="text-xs text-text-secondary-dark">Nivel 3 - Zona Este</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3 rounded-lg border border-yellow-500/50 bg-yellow-900/20">
                        <span class="material-icons text-yellow-400 text-sm">warning</span>
                        <div>
                            <p class="font-medium text-text-dark text-sm">Cableado Expuesto</p>
                            <p class="text-xs text-text-secondary-dark">Área de Servicios</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3 rounded-lg border border-orange-500/50 bg-orange-900/20">
                        <span class="material-icons text-orange-400 text-sm">warning</span>
                        <div>
                            <p class="font-medium text-text-dark text-sm">Falta EPP</p>
                            <p class="text-xs text-text-secondary-dark">Equipo de Albañilería</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Este es un comentario de Blade
    <!-- Progreso Semanal -->
    <div class="bg-card-dark p-6 rounded-lg shadow-md border border-border-dark mb-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-text-dark">Progreso Semanal</h3>
            <div class="flex gap-2">
                <button class="px-3 py-1 text-sm rounded-lg bg-primary/20 text-primary font-medium">Esta Semana</button>
                <button class="px-3 py-1 text-sm rounded-lg text-text-secondary-dark hover:bg-gray-700">Mes Actual</button>
            </div>
        </div>

        <div class="grid grid-cols-7 gap-4">
            @php
                $weekData = [
                    ['day' => 'Lun', 'progress' => 65, 'tasks' => 12],
                    ['day' => 'Mar', 'progress' => 80, 'tasks' => 15],
                    ['day' => 'Mié', 'progress' => 45, 'tasks' => 8],
                    ['day' => 'Jue', 'progress' => 90, 'tasks' => 18],
                    ['day' => 'Vie', 'progress' => 70, 'tasks' => 14],
                    ['day' => 'Sáb', 'progress' => 30, 'tasks' => 6],
                    ['day' => 'Dom', 'progress' => 0, 'tasks' => 0]
                ];
            @endphp

            @foreach($weekData as $day)
            <div class="text-center">
                <p class="text-sm text-text-secondary-dark mb-2">{{ $day['day'] }}</p>
                <div class="relative h-32 bg-gray-700 rounded-lg mb-2 overflow-hidden">
                    @if($day['progress'] > 0)
                    <div class="absolute bottom-0 left-0 right-0 bg-primary transition-all duration-500"
                         style="height: {{ $day['progress'] }}%"></div>
                    @endif
                </div>
                <p class="text-xs font-medium text-text-dark">{{ $day['tasks'] }} tareas</p>
            </div>
            @endforeach
        </div>
    </div>
    --}}

    <!-- Equipos de Trabajo -->
    <div class="bg-card-dark p-6 rounded-lg shadow-md border border-border-dark">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-text-dark">Equipos de Trabajo</h3>
            <button class="text-primary font-semibold flex items-center gap-1 hover:text-yellow-400 transition-colors">
                Gestionar equipos <span class="material-icons text-sm">groups</span>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Equipo 1 -->
            <div class="p-4 rounded-lg border border-border-dark hover:border-primary/50 transition-colors">
                <div class="flex items-center gap-3 mb-3">
                    <div class="bg-blue-900/50 p-2 rounded-full">
                        <span class="material-icons text-blue-400">construction</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-text-dark">Albañilería</h4>
                        <p class="text-xs text-text-secondary-dark">6 miembros</p>
                    </div>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-text-secondary-dark">Proyecto:</span>
                    <span class="text-primary font-medium">El Roble</span>
                </div>
            </div>

            <!-- Equipo 2 -->
            <div class="p-4 rounded-lg border border-border-dark hover:border-primary/50 transition-colors">
                <div class="flex items-center gap-3 mb-3">
                    <div class="bg-green-900/50 p-2 rounded-full">
                        <span class="material-icons text-green-400">electric_bolt</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-text-dark">Electricistas</h4>
                        <p class="text-xs text-text-secondary-dark">4 miembros</p>
                    </div>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-text-secondary-dark">Proyecto:</span>
                    <span class="text-primary font-medium">Plaza del Sol</span>
                </div>
            </div>

            <!-- Equipo 3 -->
            <div class="p-4 rounded-lg border border-border-dark hover:border-primary/50 transition-colors">
                <div class="flex items-center gap-3 mb-3">
                    <div class="bg-purple-900/50 p-2 rounded-full">
                        <span class="material-icons text-purple-400">plumbing</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-text-dark">Fontaneros</h4>
                        <p class="text-xs text-text-secondary-dark">3 miembros</p>
                    </div>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-text-secondary-dark">Proyecto:</span>
                    <span class="text-primary font-medium">CorpCenter</span>
                </div>
            </div>

            <!-- Equipo 4 -->
            <div class="p-4 rounded-lg border border-border-dark hover:border-primary/50 transition-colors">
                <div class="flex items-center gap-3 mb-3">
                    <div class="bg-yellow-900/50 p-2 rounded-full">
                        <span class="material-icons text-yellow-400">architecture</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-text-dark">Carpinteros</h4>
                        <p class="text-xs text-text-secondary-dark">5 miembros</p>
                    </div>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-text-secondary-dark">Proyecto:</span>
                    <span class="text-primary font-medium">El Roble</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .material-icons {
        font-size: 1.25rem;
    }
    .material-icons.text-sm {
        font-size: 1rem;
    }
</style>
@endpush
