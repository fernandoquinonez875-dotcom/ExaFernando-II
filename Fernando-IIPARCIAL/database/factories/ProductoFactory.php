<?php

namespace Database\Factories;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition(): array
    {
        return [
            'id' => fake()->unique()->uuid(),
            'codigo' => fake()->unique()->bothify('PRO-####-????'),
            'nombre' => fake()->randomElement(['Laptop', 'Monitor', 'Impresora', 'Teclado', 'Proyector']),
            'marca' => fake()->randomElement(['Dell', 'HP', 'Lenovo', 'Epson', 'Logitech']),
            'fecha_adquisicion' => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'estado' => fake()->randomElement(Producto::ESTADOS),
        ];
    }
}
