@extends('layouts.app')

@section('contenido')
    <h1>Registrar producto</h1>

    <form method="POST" action="{{ route('productos.store') }}">
        @csrf
        <p>
            <label for="id">ID</label>
            <input type="text" id="id" name="id" value="{{ old('id') }}" maxlength="255" required>
        </p>
        <p>
            <label for="codigo">Codigo</label>
            <input type="text" id="codigo" name="codigo" value="{{ old('codigo') }}" maxlength="255" required>
        </p>
        <p>
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" maxlength="255" required>
        </p>
        <p>
            <label for="marca">Marca</label>
            <input type="text" id="marca" name="marca" value="{{ old('marca') }}" maxlength="255" required>
        </p>
        <p>
            <label for="fecha_adquisicion">Fecha de adquisicion</label>
            <input type="date" id="fecha_adquisicion" name="fecha_adquisicion" value="{{ old('fecha_adquisicion') }}" max="{{ now()->toDateString() }}" required>
        </p>
        <p>
            <label for="estado">Estado</label>
            <select id="estado" name="estado" required>
                <option value="">Seleccione un estado</option>
                @foreach (\App\Models\Producto::ESTADOS as $estado)
                    <option value="{{ $estado }}" @selected(old('estado') === $estado)>{{ ucfirst($estado) }}</option>
                @endforeach
            </select>
        </p>
        <button type="submit">Guardar producto</button>
        <a href="{{ route('productos.index') }}">Volver al listado</a>
    </form>
@endsection
