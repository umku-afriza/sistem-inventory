<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('categories.index'))->assertRedirect(route('login'));
    }

    public function test_admin_can_see_category_list_with_item_count(): void
    {
        $category = Category::factory()->has(Item::factory()->count(3))->create();

        $this->actingAs(User::factory()->create())
            ->get(route('categories.index'))
            ->assertOk()
            ->assertSee($category->name)
            ->assertSee('3 bahan');
    }

    public function test_admin_can_create_category(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('categories.store'), ['name' => 'Alat Tulis', 'description' => 'Kertas, pulpen'])
            ->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', ['name' => 'Alat Tulis']);
    }

    public function test_category_name_must_be_unique(): void
    {
        Category::factory()->create(['name' => 'Elektronik']);

        $this->actingAs(User::factory()->create())
            ->post(route('categories.store'), ['name' => 'Elektronik'])
            ->assertSessionHasErrors('name');
    }

    public function test_admin_can_update_category_keeping_its_own_name(): void
    {
        $category = Category::factory()->create(['name' => 'Elektronik']);

        $this->actingAs(User::factory()->create())
            ->put(route('categories.update', $category), ['name' => 'Elektronik', 'description' => 'Baru'])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('categories.index'));

        $this->assertSame('Baru', $category->fresh()->description);
    }

    public function test_category_in_use_cannot_be_deleted(): void
    {
        $item = Item::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('categories.destroy', $item->category))
            ->assertSessionHas('error');

        $this->assertModelExists($item->category);
    }

    public function test_unused_category_can_be_deleted(): void
    {
        $category = Category::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('categories.destroy', $category))
            ->assertRedirect(route('categories.index'));

        $this->assertModelMissing($category);
    }
}
