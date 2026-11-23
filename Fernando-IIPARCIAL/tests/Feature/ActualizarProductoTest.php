<?php

namespace Tests\Feature;

use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActualizarProductoTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_edit_or_update_products(): void
    {
        $producto = Producto::factory()->create();
        $this->get(route('productos.edit', $producto))->assertRedirect(route('login'));
        $this->put(route('productos.update', $producto), ['nombre' => 'Cambio'])->assertRedirect(route('login'));
        $this->assertSame($producto->nombre, $producto->fresh()->nombre);
    }

    public function test_form_shows_current_values_and_accepts_text_ids(): void
    {
        $producto = Producto::factory()->create(['id' => '0001', 'estado' => 'mantenimiento']);
        $this->actingAs(User::factory()->create())->get(route('productos.edit', $producto))
            ->assertOk()->assertSee('Editar producto')->assertSee('Actualizar producto')
            ->assertSee('value="0001" readonly', false)
            ->assertSee($producto->codigo)->assertSee($producto->nombre)->assertSee($producto->marca)
            ->assertSee($producto->fecha_adquisicion->format('Y-m-d'))
            ->assertSee('value="mantenimiento" selected', false);
    }

    public function test_updates_keep_the_same_code_id_and_creation_time(): void
    {
        $producto = Producto::factory()->create(['id' => '0001', 'codigo' => 'PRO-001']);
        $createdAt = $producto->created_at->toDateTimeString();
        $this->travel(1)->minutes();
        $datos = $this->data($producto);
        $this->actingAs(User::factory()->create())->put(route('productos.update', $producto), $datos + [
            'id' => 'OTRO', 'created_at' => '2000-01-01', 'updated_at' => '2000-01-01',
        ])->assertRedirect(route('productos.index'))->assertSessionHasNoErrors()
            ->assertSessionHas('exito', 'Producto actualizado correctamente.');
        $actualizado = $producto->fresh();
        $this->assertSame('0001', $actualizado->id);
        $this->assertSame($createdAt, $actualizado->created_at->toDateTimeString());
        $this->assertTrue($actualizado->updated_at->greaterThan($producto->updated_at));
        $this->assertSame('2020-01-01', $actualizado->fecha_adquisicion->toDateString());
        unset($datos['fecha_adquisicion']);
        $this->assertDatabaseHas('productos', $datos + ['id' => '0001']);
        $this->assertDatabaseCount('productos', 1);
        $this->get(route('productos.index'))->assertOk()->assertSee('Monitor actualizado');
    }

    public function test_duplicate_codes_and_invalid_fields_do_not_change_the_product(): void
    {
        $producto = Producto::factory()->create();
        $otro = Producto::factory()->create(['codigo' => 'PRO-002']);
        $this->actingAs(User::factory()->create());
        $datos = array_replace($this->data($producto), [
            'codigo' => $otro->codigo, 'nombre' => '', 'marca' => str_repeat('a', 256),
            'fecha_adquisicion' => now()->addDay()->toDateString(), 'estado' => 'vendido',
        ]);
        $this->from(route('productos.edit', $producto))
            ->put(route('productos.update', $producto), $datos)
            ->assertRedirect(route('productos.edit', $producto))
            ->assertSessionHasErrors(['codigo', 'nombre', 'marca', 'fecha_adquisicion', 'estado'])
            ->assertSessionHasInput('codigo', $otro->codigo);
        $this->assertEquals($producto->getAttributes(), $producto->fresh()->getAttributes());
        $this->get(route('productos.edit', $producto))->assertOk()->assertSee($otro->codigo);
    }

    public function test_code_can_be_changed_to_an_unused_value(): void
    {
        $producto = Producto::factory()->create();
        $datos = array_replace($this->data($producto), ['codigo' => 'NUEVO-001']);
        $this->actingAs(User::factory()->create())->put(route('productos.update', $producto), $datos)
            ->assertRedirect(route('productos.index'))->assertSessionHasNoErrors();
        $this->assertSame('NUEVO-001', $producto->fresh()->codigo);
    }

    public function test_codes_longer_than_ten_characters_are_rejected(): void
    {
        $producto = Producto::factory()->create(['codigo' => 'PRO-001']);
        $datos = array_replace($this->data($producto), ['codigo' => str_repeat('A', 11)]);
        $this->actingAs(User::factory()->create())->put(route('productos.update', $producto), $datos)
            ->assertSessionHasErrors(['codigo' => 'El campo codigo no debe superar los 10 caracteres.']);
        $this->assertSame('PRO-001', $producto->fresh()->codigo);
    }

    public function test_missing_products_return_404(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get(route('productos.edit', 'no-existe'))->assertNotFound();
        $this->put(route('productos.update', 'no-existe'), [])->assertNotFound();
    }

    private function data(Producto $producto): array
    {
        return [
            'codigo' => $producto->codigo,
            'nombre' => 'Monitor actualizado',
            'marca' => 'Dell',
            'fecha_adquisicion' => '2020-01-01',
            'estado' => 'asignado',
        ];
    }
}
