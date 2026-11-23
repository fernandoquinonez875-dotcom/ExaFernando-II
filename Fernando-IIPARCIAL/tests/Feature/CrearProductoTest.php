<?php

namespace Tests\Feature;

use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CrearProductoTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_open_the_form_or_create_products(): void
    {
        $this->get(route('productos.create'))->assertRedirect(route('login'));
        $this->post(route('productos.store'), [])->assertRedirect(route('login'));
        $this->assertDatabaseCount('productos', 0);
    }

    public function test_authenticated_users_can_open_the_creation_form(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('productos.create'))->assertOk()
            ->assertSee('Registrar producto')->assertSee('Guardar producto')
            ->assertSee('name="id"', false)->assertSee('name="codigo"', false)
            ->assertSee('name="nombre"', false)->assertSee('name="marca"', false)
            ->assertSee('name="fecha_adquisicion"', false)->assertSee('name="estado"', false)
            ->assertDontSee('name="created_at"', false)->assertDontSee('name="updated_at"', false);
    }

    #[DataProvider('states')]
    public function test_valid_products_are_saved_with_text_ids_and_automatic_timestamps(string $estado): void
    {
        $this->travelTo(now()->setDate(2026, 11, 23)->setTime(12, 0));
        $datos = $this->validData(['estado' => $estado]);
        $this->actingAs(User::factory()->create());

        $this->post(route('productos.store'), $datos + [
            'created_at' => '2000-01-01', 'updated_at' => '2000-01-01',
        ])->assertRedirect(route('productos.index'))
            ->assertSessionHasNoErrors()->assertSessionHas('exito', 'Producto registrado correctamente.');

        $this->assertDatabaseHas('productos', [
            'id' => '0001', 'codigo' => 'PRO-001', 'nombre' => 'Laptop', 'marca' => 'Dell',
            'estado' => $estado, 'created_at' => '2026-11-23 12:00:00', 'updated_at' => '2026-11-23 12:00:00',
        ]);
        $producto = Producto::firstOrFail();
        $this->assertSame('0001', $producto->id);
        $this->assertSame('2026-11-23', $producto->fecha_adquisicion->toDateString());
        $this->get(route('productos.index'))->assertOk()->assertSee('0001')->assertSee('PRO-001');
    }

    public static function states(): array
    {
        return [['disponible'], ['asignado'], ['mantenimiento']];
    }

    public function test_required_fields_are_rejected_when_missing(): void
    {
        $this->actingAs(User::factory()->create())->post(route('productos.store'), [])
            ->assertSessionHasErrors([
                'id' => 'El campo id es obligatorio.',
                'codigo' => 'El campo codigo es obligatorio.',
                'nombre' => 'El campo nombre es obligatorio.',
                'marca' => 'El campo marca es obligatorio.',
                'fecha_adquisicion' => 'El campo fecha adquisicion es obligatorio.',
                'estado' => 'El campo estado es obligatorio.',
            ]);
        $this->assertDatabaseCount('productos', 0);
    }

    #[DataProvider('invalidFields')]
    public function test_invalid_values_are_rejected_and_input_is_preserved(string $campo, mixed $valor, string $mensaje): void
    {
        $this->travelTo(now()->setDate(2026, 11, 23)->setTime(12, 0));
        $this->actingAs(User::factory()->create());

        $this->from(route('productos.create'))->post(route('productos.store'), $this->validData([$campo => $valor]))
            ->assertRedirect(route('productos.create'))->assertSessionHasErrors([$campo => $mensaje])
            ->assertSessionHasInput('nombre');

        $this->assertDatabaseCount('productos', 0);
    }

    public static function invalidFields(): array
    {
        return [
            'numeric id' => ['id', 123, 'El campo id debe ser texto.'],
            'numeric code' => ['codigo', 123, 'El campo codigo debe ser texto.'],
            'numeric name' => ['nombre', 123, 'El campo nombre debe ser texto.'],
            'numeric brand' => ['marca', 123, 'El campo marca debe ser texto.'],
            'long id' => ['id', str_repeat('a', 256), 'El campo id no debe superar los 255 caracteres.'],
            'long code' => ['codigo', str_repeat('a', 256), 'El campo codigo no debe superar los 255 caracteres.'],
            'long name' => ['nombre', str_repeat('a', 256), 'El campo nombre no debe superar los 255 caracteres.'],
            'long brand' => ['marca', str_repeat('a', 256), 'El campo marca no debe superar los 255 caracteres.'],
            'future date' => ['fecha_adquisicion', '2026-11-24', 'La fecha de adquisicion no puede ser futura.'],
            'invalid date' => ['fecha_adquisicion', '2026-02-30', 'La fecha de adquisicion debe ser una fecha valida (AAAA-MM-DD).'],
            'unknown state' => ['estado', 'vendido', 'El estado debe ser disponible, asignado o mantenimiento.'],
        ];
    }

    public function test_duplicate_id_and_code_are_rejected(): void
    {
        $this->travelTo(now()->setDate(2026, 11, 23)->setTime(12, 0));
        Producto::factory()->create(['id' => '0001', 'codigo' => 'PRO-001']);
        $this->actingAs(User::factory()->create());

        $this->post(route('productos.store'), $this->validData())->assertSessionHasErrors([
            'id' => 'El id ya esta registrado.', 'codigo' => 'El codigo ya esta registrado.',
        ]);

        $this->assertDatabaseCount('productos', 1);
    }

    private function validData(array $overrides = []): array
    {
        return array_replace([
            'id' => '0001', 'codigo' => 'PRO-001', 'nombre' => 'Laptop', 'marca' => 'Dell',
            'fecha_adquisicion' => '2026-11-23', 'estado' => 'disponible',
        ], $overrides);
    }
}
