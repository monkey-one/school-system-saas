<div class="flex flex-wrap items-center justify-center gap-3 bg-amber-500 px-4 py-2 text-sm font-medium text-white">
    <span>{{ __('You are viewing :school as Super Admin.', ['school' => $tenant?->name ?? '-']) }}</span>
    <a href="{{ route('impersonate.stop') }}" class="rounded-md bg-white/20 px-3 py-1 font-semibold hover:bg-white/30">
        {{ __('Back to Super Admin') }}
    </a>
</div>
