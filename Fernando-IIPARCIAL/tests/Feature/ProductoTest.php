<?php

namespace Tests\Feature;

use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('productos.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_users_see_products_and_all_their_fields(): void
    {
        $producto = Producto::factory()->create(['id' => '0001', 'nombre' => '<script>alert(1)</script>']);
        $this->actingAs(User::factory()->create());

        $this->get(route('productos.index'))->assertOk()
            ->assertSee('Productos registrados')->assertSee('0001')
            ->assertSee($producto->codigo)->assertSee($producto->nombre)
            ->assertDontSee($producto->nombre, false)
            ->assertSee($producto->marca)->assertSee($producto->estado)
            ->assertSee($producto->fecha_adquisicion->format('d/m/Y'))
            ->assertSee($producto->created_at->toDateTimeString())
            ->assertSee($producto->updated_at->toDateTimeString());
        $this->get(route('inicio'))->assertRedirect(route('productos.index'));
    }

    public function test_empty_list_displays_a_message(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('productos.index'))->assertOk()->assertSee('No hay productos registrados.');
    }

    public function test_editing_and_deletion_are_unavailable(): void
    {
        $producto = Producto::factory()->create();
        $this->actingAs(User::factory()->create());

        $this->get(route('productos.index'))->assertOk()
            ->assertSee('Registrar producto')->assertDontSee('Editar')->assertDontSee('Eliminar');
        $this->get('/productos/'.$producto->id.'/edit')->assertNotFound();
        $this->put('/productos/'.$producto->id, [])->assertNotFound();
        $this->delete('/productos/'.$producto->id)->assertNotFound();

        $this->assertDatabaseCount('productos', 1);
        $this->assertModelExists($producto);
    }
}
