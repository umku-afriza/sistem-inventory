<?php

namespace Tests\Feature;

use App\Enums\MovementType;
use App\Models\Item;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_enough_data_for_pagination_demo(): void
    {
        $this->seed();

        $this->assertGreaterThan(10, User::count());
        $this->assertGreaterThan(10, Item::count());
        $this->assertGreaterThan(0, Item::lowStock()->count());
    }

    public function test_seeded_item_stock_matches_its_movement_history(): void
    {
        $this->seed();

        Item::with('stockMovements')->get()->each(function (Item $item) {
            $movements = $item->stockMovements;
            $expectedStock = $movements->where('type', MovementType::In)->sum('quantity')
                - $movements->where('type', MovementType::Out)->sum('quantity');

            $this->assertSame($expectedStock, $item->stock, "Stok {$item->code} tidak sesuai riwayat");
        });
    }

    public function test_seeder_can_be_run_twice_without_duplicates(): void
    {
        $this->seed();
        $counts = [User::count(), Item::count(), StockMovement::count()];

        $this->seed();

        $this->assertSame($counts, [User::count(), Item::count(), StockMovement::count()]);
    }
}
