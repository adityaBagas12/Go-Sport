@if (session('success') || session('error') || $errors->any())
    <div class="container-page fixed inset-x-0 top-24 z-50 flex flex-col gap-3">
        @if (session('success'))
            <div data-flash class="mx-auto flex w-full max-w-xl items-start gap-3 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 shadow-lg">
                <x-icon name="check-circle" class="mt-0.5 h-5 w-5 shrink-0 text-green-600" />
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div data-flash class="mx-auto flex w-full max-w-xl items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 shadow-lg">
                <x-icon name="x" class="mt-0.5 h-5 w-5 shrink-0 text-red-600" />
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div data-flash class="mx-auto flex w-full max-w-xl items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 shadow-lg">
                <x-icon name="x" class="mt-0.5 h-5 w-5 shrink-0 text-red-600" />
                <ul class="space-y-0.5 text-sm font-medium text-red-800">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif
