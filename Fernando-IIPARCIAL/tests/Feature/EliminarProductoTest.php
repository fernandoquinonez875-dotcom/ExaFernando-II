<?php

namespace Tests\Feature;

use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EliminarProductoTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_delete_products(): void
    {
        $producto = Producto::factory()->create();

        $this->delete(route('productos.destroy', $producto))->assertRedirect(route('login'));

        $this->assertModelExists($producto);
    }

    public function test_authenticated_users_can_delete_only_the_selected_product(): void
    {
        $producto = Producto::factory()->create(['id' => '0001']);
        $otro = Producto::factory()->create(['id' => '0002']);
        $this->actingAs(User::factory()->create());

        $this->delete(route('productos.destroy', $producto))
            ->assertRedirect(route('productos.index'))
            ->assertSessionHas('exito', 'Producto eliminado correctamente.');

        $this->assertModelMissing($producto);
        $this->assertModelExists($otro);
        $this->assertDatabaseCount('productos', 1);
        $this->get(route('productos.index'))->assertOk()->assertDontSee('0001')->assertSee('0002');
    }

    public function test_deleting_the_last_product_shows_the_empty_list(): void
    {
        $producto = Producto::factory()->create();
        $this->actingAs(User::factory()->create());

        $this->delete(route('productos.destroy', $producto))->assertRedirect(route('productos.index'));

        $this->get(route('productos.index'))->assertOk()->assertSee('No hay productos registrados.');
        $this->assertDatabaseCount('productos', 0);
    }

    public function test_missing_products_return_404_without_deleting_other_products(): void
    {
        $producto = Producto::factory()->create();
        $this->actingAs(User::factory()->create());

        $this->delete(route('productos.destroy', 'no-existe'))->assertNotFound();

        $this->assertModelExists($producto);
    }

    public function test_list_contains_a_delete_form_with_confirmation_and_csrf_token(): void
    {
        $producto = Producto::factory()->create();

        $this->actingAs(User::factory()->create())->get(route('productos.index'))
            ->assertOk()->assertSee(route('productos.destroy', $producto))
            ->assertSee('onsubmit="return confirm(', false)
            ->assertSee('name="_token"', false)
            ->assertSee('name="_method" value="DELETE"', false)
            ->assertSee('Eliminar');
    }
}
