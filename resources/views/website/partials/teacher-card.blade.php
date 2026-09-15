<div class="rounded-2xl border border-gray-100 bg-white p-5 text-center shadow-sm">
    @if ($teacher->photo)
        <img src="{{ asset('storage/' . $teacher->photo) }}" alt="{{ $teacher->full_name }}" loading="lazy" class="mx-auto h-24 w-24 rounded-full object-cover">
    @else
        <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-navy-100 font-heading text-3xl font-bold text-navy-600">{{ mb_substr($teacher->full_name, 0, 1) }}</div>
    @endif
    <p class="mt-3 font-semibold text-gray-800">{{ $teacher->full_name }}</p>
    <p class="text-sm text-gray-500">{{ $teacher->position ?? __('Teacher') }}</p>
    @if ($teacher->major)<p class="mt-1 text-xs text-gray-400">{{ $teacher->major }}</p>@endif
</div>
