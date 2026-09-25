<?php

namespace Tests\Feature;

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_returns_paginated_items(): void
    {
        Item::factory()->count(3)->create();

        $this->getJson('/api/items')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [['id', 'code', 'name', 'category', 'unit', 'stock', 'is_low_stock']],
                'links',
                'meta',
            ]);
    }

    public function test_api_can_filter_low_stock_items(): void
    {
        Item::factory()->lowStock()->create();
        Item::factory()->create(['stock' => 50, 'min_stock' => 5]);

        $this->getJson('/api/items?low_stock=1')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.is_low_stock', true);
    }

    public function test_api_returns_single_item(): void
    {
        $item = Item::factory()->create();

        $this->getJson("/api/items/{$item->id}")
            ->assertOk()
            ->assertJsonPath('data.code', $item->code)
            ->assertJsonPath('data.category', $item->category->name);
    }

    public function test_api_returns_404_for_missing_item(): void
    {
        $this->getJson('/api/items/999')->assertNotFound();
    }
}
