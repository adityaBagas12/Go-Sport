<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Menyusun data "Grafik Penjualan" pada dashboard admin.
 *
 * Satu bucket = satu titik pada grafik. Bucket yang tidak punya transaksi
 * tetap dikembalikan dengan nilai 0 supaya garis grafik tidak terputus.
 */
class SalesChartService
{
    public const PERIODS = [
        'week' => '1 Minggu',
        'month' => '1 Bulan',
        'quarter' => '3 Bulan',
        'half_year' => '6 Bulan',
        'year' => '1 Tahun',
    ];

    public const DEFAULT_PERIOD = 'year';

    /**
     * @return array{
     *     period: string,
     *     period_label: string,
     *     subtitle: string,
     *     total: int,
     *     total_label: string,
     *     points: list<array{label: string, tooltip: string, revenue: int, revenue_label: string}>
     * }
     */
    public function build(?string $period = null): array
    {
        $period = $this->resolvePeriod($period);

        [$start, $end, $granularity, $count] = $this->range($period);

        $revenues = $this->revenuePerBucket($start, $end, $granularity);

        $points = [];
        $cursor = $start->copy();

        for ($index = 0; $index < $count; $index++) {
            $revenue = (int) ($revenues[$this->bucketKey($cursor, $granularity)] ?? 0);

            $points[] = [
                'label' => $this->label($cursor, $period, $granularity),
                'tooltip' => $this->tooltip($cursor, $granularity),
                'revenue' => $revenue,
                'revenue_label' => $this->rupiah($revenue),
            ];

            $granularity === 'day' ? $cursor->addDay() : $cursor->addMonth();
        }

        $total = array_sum(array_column($points, 'revenue'));

        return [
            'period' => $period,
            'period_label' => self::PERIODS[$period],
            'subtitle' => 'Pendapatan berdasarkan periode',
            'total' => $total,
            'total_label' => $this->rupiah($total),
            'points' => $points,
        ];
    }

    public function resolvePeriod(?string $period): string
    {
        return isset(self::PERIODS[$period]) ? $period : self::DEFAULT_PERIOD;
    }

    /**
     * @return array{0: Carbon, 1: Carbon, 2: string, 3: int}
     */
    private function range(string $period): array
    {
        $now = Carbon::now();

        return match ($period) {
            'week' => [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay(), 'day', 7],
            'month' => [$now->copy()->subDays(29)->startOfDay(), $now->copy()->endOfDay(), 'day', 30],
            'quarter' => [$now->copy()->startOfMonth()->subMonths(2), $now->copy()->endOfMonth(), 'month', 3],
            'half_year' => [$now->copy()->startOfMonth()->subMonths(5), $now->copy()->endOfMonth(), 'month', 6],
            'year' => [$now->copy()->startOfMonth()->subMonths(11), $now->copy()->endOfMonth(), 'month', 12],
        };
    }

    /**
     * Total pendapatan per bucket, digabung langsung di database.
     *
     * @return array<string, int|float>
     */
    private function revenuePerBucket(Carbon $start, Carbon $end, string $granularity): array
    {
        $expression = $this->bucketExpression($granularity);

        return Order::query()
            ->countedAsRevenue()
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw("{$expression} as bucket, SUM(total) as revenue")
            ->groupByRaw($expression)
            ->pluck('revenue', 'bucket')
            ->all();
    }

    private function bucketExpression(string $granularity): string
    {
        $driver = DB::connection()->getDriverName();

        if ($granularity === 'day') {
            return match ($driver) {
                'sqlite' => "strftime('%Y-%m-%d', created_at)",
                'pgsql' => 'created_at::date',
                default => 'DATE(created_at)',
            };
        }

        return match ($driver) {
            'sqlite' => "strftime('%Y-%m', created_at)",
            'pgsql' => "to_char(created_at, 'YYYY-MM')",
            default => "DATE_FORMAT(created_at, '%Y-%m')",
        };
    }

    private function bucketKey(Carbon $date, string $granularity): string
    {
        return $date->format($granularity === 'day' ? 'Y-m-d' : 'Y-m');
    }

    private function label(Carbon $date, string $period, string $granularity): string
    {
        $date = $this->localized($date);

        if ($granularity === 'day') {
            return $period === 'week'
                ? $date->translatedFormat('D')
                : $date->translatedFormat('j');
        }

        return $date->translatedFormat('M');
    }

    private function tooltip(Carbon $date, string $granularity): string
    {
        $date = $this->localized($date);

        if ($granularity === 'day') {
            return $date->translatedFormat('d M Y');
        }

        return $date->translatedFormat('F Y');
    }

    private function localized(Carbon $date): Carbon
    {
        return $date->copy()->locale('id');
    }

    private function rupiah(int $value): string
    {
        return 'Rp '.number_format($value, 0, ',', '.');
    }
}
