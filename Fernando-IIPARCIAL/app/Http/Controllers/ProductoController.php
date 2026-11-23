<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductoRequest;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductoController extends Controller
{
    public function index(): View
    {
        return view('productos.index', ['productos' => Producto::orderBy('id')->get()]);
    }

    public function create(): View
    {
        return view('productos.create');
    }

    public function store(StoreProductoRequest $request): RedirectResponse
    {
        Producto::create($request->validated());

        return redirect()->route('productos.index')->with('exito', 'Producto registrado correctamente.');
    }
}
