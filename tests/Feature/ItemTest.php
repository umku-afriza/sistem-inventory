<?php

namespace Tests\Feature;

use App\Enums\MovementType;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_filter_low_stock_items(): void
    {
        $lowItem = Item::factory()->lowStock()->create(['name' => 'Kertas Menipis']);
        $safeItem = Item::factory()->create(['name' => 'Spidol Aman', 'stock' => 50, 'min_stock' => 5]);

        $this->actingAs(User::factory()->create())
            ->get(route('items.index', ['low_stock' => 1]))
            ->assertOk()
            ->assertSee($lowItem->name)
            ->assertDontSee($safeItem->name);
    }

    public function test_creating_item_records_initial_stock_movement(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $this->actingAs($user)
            ->post(route('items.store'), [
                'category_id' => $category->id,
                'code' => 'ATK-001',
                'name' => 'Spidol',
                'unit' => 'pcs',
                'stock' => 20,
                'min_stock' => 5,
                'photo' => UploadedFile::fake()->image('spidol.jpg'),
            ])
            ->assertRedirect(route('items.index'));

        $item = Item::firstWhere('code', 'ATK-001');

        $this->assertSame(20, $item->stock);
        Storage::disk('public')->assertExists($item->photo);
        $this->assertDatabaseHas('stock_movements', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'type' => MovementType::In->value,
            'quantity' => 20,
        ]);
    }

    public function test_updating_item_does_not_change_stock(): void
    {
        $item = Item::factory()->create(['stock' => 10]);

        $this->actingAs(User::factory()->create())
            ->put(route('items.update', $item), [
                'category_id' => $item->category_id,
                'code' => $item->code,
                'name' => 'Nama Baru',
                'unit' => $item->unit,
                'min_stock' => 3,
                'stock' => 999,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('items.show', $item));

        $item->refresh();
        $this->assertSame('Nama Baru', $item->name);
        $this->assertSame(10, $item->stock);
    }

    public function test_admin_can_see_item_detail(): void
    {
        $item = Item::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('items.show', $item))
            ->assertOk()
            ->assertSee($item->code);
    }

    public function test_admin_can_delete_item(): void
    {
        $item = Item::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('items.destroy', $item))
            ->assertRedirect(route('items.index'));

        $this->assertModelMissing($item);
    }
}
