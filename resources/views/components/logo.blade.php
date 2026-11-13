@props(['class' => 'h-9'])

<a href="{{ route('home') }}" {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 group']) }}>
    <span class="grid h-9 w-9 place-items-center rounded-xl bg-neutral-900 text-brand-500 shadow-sm transition group-hover:scale-105">
        <x-icon name="bolt" class="h-5 w-5" stroke="2" />
    </span>
    <span class="text-xl font-extrabold tracking-tight text-neutral-900">
        Go<span class="text-brand-500">Sport</span>
    </span>
</a>
