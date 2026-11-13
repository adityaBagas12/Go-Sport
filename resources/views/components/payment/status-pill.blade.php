@props(['status'])

@php
    $colors = [
        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
        'processing' => 'bg-blue-50 text-blue-700 border-blue-200',
        'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'completed' => 'bg-neutral-100 text-neutral-700 border-neutral-200',
        'denied' => 'bg-red-50 text-red-700 border-red-200',
        'cancelled' => 'bg-neutral-100 text-neutral-700 border-neutral-200',
        'expired' => 'bg-orange-50 text-orange-700 border-orange-200',
        'failed' => 'bg-red-50 text-red-700 border-red-200',
        'refunded' => 'bg-blue-50 text-blue-700 border-blue-200',
    ];
    $color = $colors[$status] ?? 'bg-neutral-100 text-neutral-700 border-neutral-200';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex rounded-full border px-4 py-1.5 text-xs font-bold '.$color]) }}>
    {{ \App\Models\Payment::statusLabel($status) }}
</span>
