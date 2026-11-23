@extends('layouts.app')

@section('contenido')
    <h1>Editar producto</h1>

    <form method="POST" action="{{ route('productos.update', $producto) }}">
        @csrf
        @method('PUT')
        <p>
            <label for="id">ID</label>
            <input type="text" id="id" value="{{ $producto->id }}" readonly>
        </p>
        <p>
            <label for="codigo">Codigo</label>
            <input type="text" id="codigo" name="codigo" value="{{ old('codigo', $producto->codigo) }}" maxlength="10" required>
        </p>
        <p>
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $producto->nombre) }}" maxlength="255" required>
        </p>
        <p>
            <label for="marca">Marca</label>
            <input type="text" id="marca" name="marca" value="{{ old('marca', $producto->marca) }}" maxlength="255" required>
        </p>
        <p>
            <label for="fecha_adquisicion">Fecha de adquisicion</label>
            <input type="date" id="fecha_adquisicion" name="fecha_adquisicion" value="{{ old('fecha_adquisicion', $producto->fecha_adquisicion->format('Y-m-d')) }}" max="{{ now()->toDateString() }}" required>
        </p>
        <p>
            <label for="estado">Estado</label>
            <select id="estado" name="estado" required>
                <option value="">Seleccione un estado</option>
                @foreach (\App\Models\Producto::ESTADOS as $estado)
                    <option value="{{ $estado }}" @selected(old('estado', $producto->estado) === $estado)>{{ ucfirst($estado) }}</option>
                @endforeach
            </select>
        </p>
        <button type="submit">Actualizar producto</button>
        <a href="{{ route('productos.index') }}">Volver al listado</a>
    </form>
@endsection
