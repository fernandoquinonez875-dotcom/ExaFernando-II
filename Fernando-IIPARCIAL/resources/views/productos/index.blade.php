@extends('layouts.app')

@section('contenido')
    <h1>Productos registrados</h1>
    <p><a href="{{ route('productos.create') }}">Registrar producto</a></p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Codigo</th>
                <th>Nombre</th>
                <th>Marca</th>
                <th>Fecha de adquisicion</th>
                <th>Estado</th>
                <th>Creado</th>
                <th>Actualizado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($productos as $producto)
                <tr>
                    <td>{{ $producto->id }}</td>
                    <td>{{ $producto->codigo }}</td>
                    <td>{{ $producto->nombre }}</td>
                    <td>{{ $producto->marca }}</td>
                    <td>{{ $producto->fecha_adquisicion->format('d/m/Y') }}</td>
                    <td>{{ $producto->estado }}</td>
                    <td>{{ $producto->created_at }}</td>
                    <td>{{ $producto->updated_at }}</td>
                </tr>
            @empty
                <tr><td colspan="8">No hay productos registrados.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
