@php
    /**
     * Grafik penjualan digambar sebagai inline SVG supaya tidak butuh library
     * charting tambahan. Sumbu Y dipetakan ke viewbox 0..100 dengan
     * preserveAspectRatio="none" sehingga grafik selalu mengikuti lebar
     * container tanpa perlu menghitung ulang ukuran lewat JavaScript.
     */
    $points = $chart['points'];
    $count = count($points);
    $maxRevenue = max(collect($points)->max('revenue') ?? 0, 1);
    $padTop = 10;
    $padBottom = 100;
    $plotHeight = $padBottom - $padTop;
    $step = $count > 1 ? 100 / ($count - 1) : 0;

    $coordinates = [];
    foreach ($points as $index => $point) {
        $coordinates[] = [
            'x' => round($index * $step, 4),
            'y' => round($padBottom - ($point['revenue'] / $maxRevenue) * $plotHeight, 4),
        ];
    }

    $linePoints = collect($coordinates)
        ->map(fn ($coordinate) => "{$coordinate['x']},{$coordinate['y']}")
        ->implode(' ');

    $areaPath = 'M 0,100 L ' . collect($coordinates)
        ->map(fn ($coordinate) => "{$coordinate['x']},{$coordinate['y']}")
        ->implode(' L ') . ' L 100,100 Z';

    $hitWidth = 100 / max($count, 1);

    // Pada periode harian 30 hari label tanggal cukup ditampilkan sebagian saja
    // supaya tidak saling tumpang tindih di layar sempit.
    $labelStride = $count > 16 ? 3 : 1;
@endphp

<div class="relative mt-8 h-56 w-full" data-sales-chart-canvas>
    <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="absolute inset-0 h-full w-full"
        role="img" aria-label="Grafik pendapatan {{ $chart['period_label'] }}">
        <defs>
            <linearGradient id="salesChartFill" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="var(--color-brand-500)" stop-opacity="0.25" />
                <stop offset="100%" stop-color="var(--color-brand-500)" stop-opacity="0" />
            </linearGradient>
        </defs>

        <line x1="0" y1="100" x2="100" y2="100" vector-effect="non-scaling-stroke"
            stroke="var(--color-neutral-200)" stroke-width="1" />

        <path d="{{ $areaPath }}" fill="url(#salesChartFill)" />
        <polyline points="{{ $linePoints }}" fill="none" vector-effect="non-scaling-stroke"
            stroke="var(--color-brand-500)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
    </svg>

    @foreach ($points as $index => $point)
        @php $coordinate = $coordinates[$index]; @endphp
        <div class="group absolute top-0 h-full" style="left: {{ $coordinate['x'] }}%; width: {{ $hitWidth }}%; transform: translateX(-50%);">
            <span class="absolute inset-0 cursor-default" tabindex="0"
                aria-label="{{ $point['tooltip'] }} - {{ $point['revenue_label'] }}"></span>

            <span
                class="pointer-events-none absolute h-2.5 w-2.5 -translate-x-1/2 -translate-y-1/2 rounded-full border-2 border-white bg-brand-500 opacity-0 shadow transition-opacity duration-200 group-hover:opacity-100 group-focus-within:opacity-100"
                style="top: {{ $coordinate['y'] }}%;"></span>

            <span
                class="pointer-events-none absolute bottom-full z-10 mb-2 hidden -translate-x-1/2 whitespace-nowrap rounded-lg bg-neutral-900 px-2 py-1 text-[10px] font-bold text-white group-hover:block group-focus-within:block">
                {{ $point['tooltip'] }} &middot; {{ $point['revenue_label'] }}
            </span>
        </div>
    @endforeach
</div>

<div class="mt-2 flex w-full">
    @foreach ($points as $index => $point)
        <span class="flex-1 truncate text-center text-[11px] font-medium text-neutral-400">
            {{ $index % $labelStride === 0 ? $point['label'] : '' }}
        </span>
    @endforeach
</div>
