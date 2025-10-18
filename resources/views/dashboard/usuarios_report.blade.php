<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Reporte de Usuarios</title>
  <style>
    body{ font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size:12px; }
    table{ width:100%; border-collapse: collapse; }
    th, td{ border:1px solid #ccc; padding:6px 8px; text-align:left; }
    th{ background:#f2f2f2; }
    h1{ text-align:center; }
    .small{ font-size:11px; color:#666; }
  </style>
</head>
<body>
  <h1>Reporte de Usuarios</h1>
  <p class="small">Generado: {{ now()->format('d/m/Y H:i') }}</p>
  @if(request()->has('role') || request()->has('from') || request()->has('to'))
    <p class="small">Filtros aplicados:
      @if(request('role') && request('role') !== 'all') Rol: {{ request('role') }}; @endif
      @if(request('from')) Desde: {{ request('from') }}; @endif
      @if(request('to')) Hasta: {{ request('to') }}; @endif
    </p>
  @endif
  <table>
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Correo</th>
        <th>Rol</th>
        <th>Fecha registro</th>
      </tr>
    </thead>
    <tbody>
      @foreach($usuarios as $u)
      <tr>
        <td>{{ $u->nombre }} {{ $u->apellido }}</td>
        <td>{{ $u->correo }}</td>
        <td>{{ ucfirst($u->rol) }}</td>
  <td>{{ $u->fecha_registro ? \Carbon\Carbon::parse($u->fecha_registro)->format('d/m/Y H:i') : '-' }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>