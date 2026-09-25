<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockMovementTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_in_increases_item_stock(): void
    {
        $item = Item::factory()->create(['stock' => 10]);

        $this->actingAs(User::factory()->create())
            ->post(route('stock-movements.store'), [
                'item_id' => $item->id,
                'type' => 'in',
                'quantity' => 5,
                'moved_at' => today()->toDateString(),
            ])
            ->assertRedirect(route('stock-movements.index'));

        $this->assertSame(15, $item->fresh()->stock);
    }

    public function test_stock_out_decreases_item_stock(): void
    {
        $item = Item::factory()->create(['stock' => 10]);

        $this->actingAs(User::factory()->create())
            ->post(route('stock-movements.store'), [
                'item_id' => $item->id,
                'type' => 'out',
                'quantity' => 4,
                'moved_at' => today()->toDateString(),
                'note' => 'Kelas Laravel',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(6, $item->fresh()->stock);
        $this->assertDatabaseHas('stock_movements', ['item_id' => $item->id, 'type' => 'out', 'note' => 'Kelas Laravel']);
    }

    public function test_stock_out_cannot_exceed_available_stock(): void
    {
        $item = Item::factory()->create(['stock' => 3]);

        $this->actingAs(User::factory()->create())
            ->post(route('stock-movements.store'), [
                'item_id' => $item->id,
                'type' => 'out',
                'quantity' => 5,
                'moved_at' => today()->toDateString(),
            ])
            ->assertSessionHasErrors('quantity');

        $this->assertSame(3, $item->fresh()->stock);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_invalid_type_and_future_date_are_rejected(): void
    {
        $item = Item::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('stock-movements.store'), [
                'item_id' => $item->id,
                'type' => 'hilang',
                'quantity' => 1,
                'moved_at' => today()->addDay()->toDateString(),
            ])
            ->assertSessionHasErrors(['type', 'moved_at']);
    }

    public function test_create_form_can_be_prefilled_from_query_string(): void
    {
        $item = Item::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('stock-movements.create', ['type' => 'out', 'item_id' => $item->id]))
            ->assertOk()
            ->assertSee($item->name);
    }
}
