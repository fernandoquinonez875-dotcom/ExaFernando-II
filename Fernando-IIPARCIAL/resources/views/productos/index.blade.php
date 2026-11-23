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
                <th>Acciones</th>
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
                    <td>
                        <a href="{{ route('productos.edit', $producto) }}">Editar</a>
                        <form method="POST" action="{{ route('productos.destroy', $producto) }}"
                              onsubmit="return confirm('¿Eliminar este producto?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9">No hay productos registrados.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
