<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Memastikan setiap halaman bisa dibuka tanpa error.
 */
class PageRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_low_stock_items_and_latest_movements(): void
    {
        $lowItem = Item::factory()->lowStock()->create(['name' => 'Kertas Menipis']);
        StockMovement::factory()->for($lowItem)->create(['note' => 'Transaksi terbaru']);

        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Kertas Menipis')
            ->assertViewHas('totalLowStock', 1);
    }

    public function test_all_inventory_pages_render(): void
    {
        $movement = StockMovement::factory()->create();
        $item = $movement->item;

        $this->actingAs(User::factory()->create());

        $urls = [
            route('categories.index'),
            route('categories.create'),
            route('categories.edit', $item->category),
            route('items.index'),
            route('items.create'),
            route('items.edit', $item),
            route('stock-movements.index', ['type' => 'in']),
            route('stock-movements.create'),
            route('reports.index'),
        ];

        foreach ($urls as $url) {
            $this->get($url)->assertOk();
        }
    }
}
