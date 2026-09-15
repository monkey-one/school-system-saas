@php
    $palette = [
        'success' => 'bg-green-100 text-green-800',
        'danger' => 'bg-red-100 text-red-800',
        'warning' => 'bg-amber-100 text-amber-800',
        'info' => 'bg-blue-100 text-blue-800',
        'gray' => 'bg-gray-100 text-gray-700',
    ];
@endphp
<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $palette[$color ?? 'gray'] ?? $palette['gray'] }}">{{ $label }}</span>
