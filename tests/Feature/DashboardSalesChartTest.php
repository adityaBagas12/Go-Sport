<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Services\SalesChartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DashboardSalesChartTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-09-29 10:00:00'));

        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    private function orderAt(Carbon $date, int $total, string $status = 'selesai'): Order
    {
        return Order::factory()->create([
            'total' => $total,
            'status' => $status,
            'payment_status' => $status === 'dibatalkan' ? 'unpaid' : 'paid',
            'created_at' => $date,
            'updated_at' => $date,
        ]);
    }

    public function test_dashboard_always_shows_the_last_twelve_months(): void
    {
        $this->orderAt(Carbon::parse('2026-09-20 12:00:00'), 750_000);

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Grafik Penjualan');

        $chart = app(SalesChartService::class)->build();

        $this->assertCount(12, $chart['points']);
        $this->assertSame('Okt', $chart['points'][0]['label']);
        $this->assertSame('Sep', $chart['points'][11]['label']);
        $this->assertSame(750_000, $chart['total']);
    }

    public function test_period_filter_is_gone_from_the_dashboard(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('name="period"', false)
            ->assertDontSee('data-sales-chart-period', false)
            ->assertDontSee('1 Minggu', false)
            ->assertDontSee('6 Bulan', false);
    }

    public function test_one_week_buckets_daily_in_indonesian_day_names(): void
    {
        $this->orderAt(Carbon::parse('2026-09-29 09:00:00'), 750_000);

        $this->actingAs($this->admin)
            ->getJson(route('admin.dashboard.sales-chart', ['period' => 'week']))
            ->assertOk()
            ->assertJsonCount(7, 'points')
            ->assertJsonPath('period', 'week')
            ->assertJsonPath('points.0.label', 'Rab')
            ->assertJsonPath('points.6.label', 'Sel')
            ->assertJsonPath('points.6.revenue', 750_000)
            ->assertJsonPath('points.6.revenue_label', 'Rp 750.000')
            ->assertJsonPath('total_label', 'Rp 750.000');
    }

    public function test_one_month_buckets_the_last_thirty_days(): void
    {
        $this->orderAt(Carbon::parse('2026-09-25 12:00:00'), 120_000);

        $response = $this->actingAs($this->admin)
            ->getJson(route('admin.dashboard.sales-chart', ['period' => 'month']))
            ->assertOk()
            ->assertJsonCount(30, 'points');

        $points = $response->json('points');

        $this->assertSame('31', $points[0]['label']);
        $this->assertSame('29', $points[29]['label']);
        $this->assertSame(120_000, $points[25]['revenue']);
        $this->assertSame(120_000, $response->json('total'));
    }

    public function test_quarter_six_months_and_year_bucket_monthly(): void
    {
        $this->orderAt(Carbon::parse('2026-08-05 12:00:00'), 400_000);
        $this->orderAt(Carbon::parse('2026-07-05 12:00:00'), 300_000);
        $this->orderAt(Carbon::parse('2026-05-05 12:00:00'), 200_000);

        $this->actingAs($this->admin)
            ->getJson(route('admin.dashboard.sales-chart', ['period' => 'quarter']))
            ->assertOk()
            ->assertJsonCount(3, 'points')
            ->assertJsonPath('points.0.label', 'Jul')
            ->assertJsonPath('points.1.label', 'Agt')
            ->assertJsonPath('points.2.label', 'Sep')
            ->assertJsonPath('total', 700_000);

        $this->actingAs($this->admin)
            ->getJson(route('admin.dashboard.sales-chart', ['period' => 'half_year']))
            ->assertOk()
            ->assertJsonCount(6, 'points')
            ->assertJsonPath('points.0.label', 'Apr')
            ->assertJsonPath('total', 900_000);

        $this->actingAs($this->admin)
            ->getJson(route('admin.dashboard.sales-chart', ['period' => 'year']))
            ->assertOk()
            ->assertJsonCount(12, 'points')
            ->assertJsonPath('points.0.label', 'Okt')
            ->assertJsonPath('total', 900_000);
    }

    public function test_cancelled_orders_are_excluded_from_every_bucket(): void
    {
        $this->orderAt(Carbon::parse('2026-09-29 09:00:00'), 500_000, 'dibatalkan');
        $this->orderAt(Carbon::parse('2026-09-28 09:00:00'), 250_000);

        $this->actingAs($this->admin)
            ->getJson(route('admin.dashboard.sales-chart', ['period' => 'week']))
            ->assertOk()
            ->assertJsonPath('points.6.revenue', 0)
            ->assertJsonPath('points.5.revenue', 250_000)
            ->assertJsonPath('total', 250_000);
    }

    public function test_buckets_without_orders_are_returned_as_zero(): void
    {
        $this->orderAt(Carbon::parse('2026-09-29 09:00:00'), 100_000);

        $points = $this->actingAs($this->admin)
            ->getJson(route('admin.dashboard.sales-chart', ['period' => 'week']))
            ->assertOk()
            ->json('points');

        foreach ($points as $index => $point) {
            if ($index === 6) {
                continue;
            }

            $this->assertSame(0, $point['revenue'], "Bucket ke-{$index} seharusnya Rp0.");
            $this->assertSame('Rp 0', $point['revenue_label']);
        }
    }

    public function test_unknown_period_falls_back_to_the_default(): void
    {
        $this->actingAs($this->admin)
            ->getJson(route('admin.dashboard.sales-chart', ['period' => 'decade']))
            ->assertOk()
            ->assertJsonPath('period', SalesChartService::DEFAULT_PERIOD)
            ->assertJsonCount(12, 'points');
    }

    public function test_graph_data_comes_from_real_orders_not_dummy_values(): void
    {
        $this->orderAt(Carbon::parse('2026-09-20 08:00:00'), 1_500_000);
        $this->orderAt(Carbon::parse('2026-09-20 18:00:00'), 2_500_000);

        $this->actingAs($this->admin)
            ->getJson(route('admin.dashboard.sales-chart', ['period' => 'month']))
            ->assertOk()
            ->assertJsonPath('points.20.revenue', 4_000_000)
            ->assertJsonPath('total', 4_000_000);
    }

    public function test_total_revenue_badge_covers_the_last_twelve_months(): void
    {
        $this->orderAt(Carbon::parse('2026-09-20 08:00:00'), 1_500_000);
        $this->orderAt(Carbon::parse('2025-12-05 08:00:00'), 9_000_000);

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Rp 10.500.000');
    }

    public function test_guests_are_redirected_from_the_chart_endpoint(): void
    {
        $this->get(route('admin.dashboard.sales-chart', ['period' => 'week']))
            ->assertRedirect(route('login'));

        $this->actingAs(User::factory()->create(['role' => 'customer']))
            ->get(route('admin.dashboard.sales-chart', ['period' => 'week']))
            ->assertForbidden();
    }
}
