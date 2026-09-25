<?php

namespace Tests\Feature;

use App\Enums\MovementType;
use App\Models\Item;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_sums_movements_within_period_only(): void
    {
        $item = Item::factory()->create();
        StockMovement::factory()->for($item)->create(['type' => MovementType::In, 'quantity' => 7, 'moved_at' => '2026-01-10']);
        StockMovement::factory()->for($item)->create(['type' => MovementType::Out, 'quantity' => 2, 'moved_at' => '2026-01-31']);
        StockMovement::factory()->for($item)->create(['type' => MovementType::In, 'quantity' => 100, 'moved_at' => '2026-02-01']);

        $response = $this->actingAs(User::factory()->create())
            ->get(route('reports.index', ['start_date' => '2026-01-01', 'end_date' => '2026-01-31']))
            ->assertOk();

        $reportItem = $response->viewData('items')->firstWhere('id', $item->id);
        $this->assertEquals(7, $reportItem->total_in);
        $this->assertEquals(2, $reportItem->total_out);
    }

    public function test_end_date_must_not_be_before_start_date(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('reports.index', ['start_date' => '2026-02-01', 'end_date' => '2026-01-01']))
            ->assertSessionHasErrors('end_date');
    }

    public function test_report_can_be_exported_as_csv(): void
    {
        $item = Item::factory()->create(['code' => 'ATK-999']);

        $response = $this->actingAs(User::factory()->create())
            ->get(route('reports.export', ['start_date' => '2026-01-01', 'end_date' => '2026-01-31']))
            ->assertOk()
            ->assertDownload('laporan-stok-20260101-20260131.csv');

        $this->assertStringContainsString('ATK-999', $response->streamedContent());
    }
}
