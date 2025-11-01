@extends('layouts.app-proveedor')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold text-white mb-6">Estadísticas - Tus ventas</h1>

    @if(!$proveedor)
        <div class="bg-card-blue p-4 rounded text-white">No se encontró un registro de proveedor para tu usuario.</div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-card-blue p-4 rounded text-white">
                <div class="text-sm">Saldo total</div>
                <div class="text-2xl font-bold">${{ number_format($summary->total_revenue ?? 0,2) }}</div>
            </div>
            <div class="bg-card-blue p-4 rounded text-white">
                <div class="text-sm">Unidades vendidas</div>
                <div class="text-2xl font-bold">{{ $summary->total_units ?? 0 }}</div>
            </div>
            <div class="bg-card-blue p-4 rounded text-white">
                <div class="text-sm">Proyectos compradores</div>
                <div class="text-2xl font-bold">{{ $summary->projects_count ?? 0 }}</div>
            </div>
        </div>

        <div class="mb-6">
            <h2 class="text-lg font-semibold text-white mb-2">Materiales vendidos</h2>
            <div class="bg-card-blue rounded overflow-x-auto">
                <table class="w-full text-left text-white">
                    <thead class="bg-[#20304A]"><tr><th class="p-3">Material</th><th class="p-3">Unidades</th><th class="p-3">Ingresos</th></tr></thead>
                    <tbody>
                        @foreach($materials as $m)
                            <tr class="border-b border-accent-gold/6">
                                <td class="p-3">{{ $m->nombre }}</td>
                                <td class="p-3">{{ $m->units_sold }}</td>
                                <td class="p-3">${{ number_format($m->revenue,2) }}</td>
                            </tr>
                        @endforeach
                        @if($materials->isEmpty())
                            <tr><td class="p-3 text-center" colspan="3">No hay ventas registradas.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <h2 class="text-lg font-semibold text-white mb-2">Proyectos que compraron</h2>
            <div class="bg-card-blue rounded overflow-x-auto">
                <table class="w-full text-left text-white">
                    <thead class="bg-[#20304A]"><tr><th class="p-3">Proyecto</th><th class="p-3">Contratista</th><th class="p-3">Total gastado</th></tr></thead>
                    <tbody>
                        @foreach($projects as $p)
                            <tr class="border-b border-accent-gold/6">
                                <td class="p-3">{{ $p->proyecto_nombre }}</td>
                                <td class="p-3">{{ $p->contratista ?? '-' }}</td>
                                <td class="p-3">${{ number_format($p->total_spent,2) }}</td>
                            </tr>
                        @endforeach
                        @if($projects->isEmpty())
                            <tr><td class="p-3 text-center" colspan="3">Ningún proyecto ha comprado tus materiales aún.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
