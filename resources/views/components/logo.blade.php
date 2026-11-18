@props(['class' => 'h-9', 'dark' => false])

<a href="{{ route('home') }}" {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 group']) }}>
    <span class="grid h-9 w-9 place-items-center rounded-xl bg-[#4F7A63] text-white shadow-sm transition group-hover:scale-105 group-hover:bg-[#3E6350]">
        <x-icon name="bolt" class="h-5 w-5" stroke="2" />
    </span>
    <span class="text-xl font-extrabold tracking-tight {{ $dark ? 'text-white' : 'text-[#1E2B24]' }}">
        Go<span class="text-[#4F7A63]">Sport</span>
    </span>
</a>
