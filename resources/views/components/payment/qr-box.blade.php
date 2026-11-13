@props(['payment'])

<div {{ $attributes->merge(['class' => 'grid place-items-center rounded-2xl bg-white p-6']) }}>
    {!! $payment->qr_code !!}
</div>